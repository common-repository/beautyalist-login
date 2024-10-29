<?php

namespace BeautyAListLogin\Controllers;

use BeautyAListLogin\Helpers\Storage;
use BeautyAListLogin\Helpers\Util;
use BeautyAListLogin\Services\API;
use BeautyAListLogin\Services\Service;
use BeautyAListLogin\Services\ViewService;

class Front
{
    private string $api_key          = '';
    private string $client_id        = '';
    private string $page             = '';
    private string $role             = '';

    private Util $util;
    private Storage $storage;

    private string $licences_option_name       = 'bl_login_licences';
    private string $match_option_name          = 'bl_login_match';
    private string $tag_option_name            = 'bl_login_tag';

    private string $default_tag     = 'beautyalist';

    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'add_assets']);
        add_action('wp_footer', [$this, 'insert_header_code']);
        add_action('wp_ajax_nopriv_bl_login_login', [$this, 'handle_bl_login_login']);
        add_action('wp_ajax_bl_login_login', [$this, 'handle_bl_login_login']);
        add_action('edit_user_profile', [$this, 'extra_profile_fields']);
        add_action('rest_api_init', [$this, 'register_route']);

        // Checked permission for tag update
        add_action('personal_options_update', [$this, 'save_extra_profile_fields']);
        add_action('edit_user_profile_update', [$this, 'save_extra_profile_fields']);

        list($this->api_key, $this->client_id, $this->page, $this->role) = Util::set_options();

        $this->util    = new Util();
        $this->storage = new Storage();
    }

    public function add_assets()
    {
        $version = '1.0.2';

        if ($this->storage->get_data() && is_front_page()) {
            $dir = plugin_dir_url(__FILE__).'../../assets/';

            wp_enqueue_script('bl-plugin-script', $dir.'js/script.js', ['jquery'], $version, true);
            wp_enqueue_script('bl-plugin-script-bootstrap', $dir.'js/vendors/bootstrap.bundle.min.js', [], $version, true);

            wp_enqueue_style('bl-plugin-style-bootstrap-io', $dir.'css/bootstrap-iso.css', false, $version);
            wp_enqueue_style('bl-plugin-style', $dir.'css/styles.css', false, $version);
            wp_enqueue_style('bl-plugin-style-overlay', $dir.'css/overlay.css', false, $version);
        }
    }

    public function register_route()
    {
        $data = [
            'methods'             => ['GET'],
            'callback'            => [$this, 'route_callback'],
            'args'                => $this->util->prepare_params(),
            'permission_callback' => '__return_true',
        ];

        register_rest_route('beautyalist_login/v1', '/callback', $data, true);
    }

    public function route_callback($request)
    {
        $url    = home_url();
        $params = $request->get_params();
        if (!empty($params['code'])) {
            $instance = new API($this->client_id, $this->api_key);
            $data     = $instance->getToken($params['code']);
            if ($data && !empty($data['access_token'])) {
                $user_info = $instance->getUserData($data['access_token']);
                if ($user_info) {
                    $service = new Service();
                    $res     = $service->proceed_user($user_info, $this->role);
                    if (!$res['status']) {
                        if ('email_exist' == $res['error_code']) {
                            $this->storage->set_data($data['access_token'], $res['email']);
                            $url .= '/?beautyalist-login=confirm-account';
                        }
                    } else {
                        // wp_new_user_notification( $user_id, null,'both');
                        wp_set_auth_cookie($res['user']->ID, false, is_ssl());
                        wp_set_current_user($res['user']->ID, $res['user']->user_login);
                        do_action('wp_login', $res['user']->user_login, $res['user']);

                        // my profile
                        $url = apply_filters('beautyalist_login_redirect_url', admin_url('profile.php'));
                    }
                }
            }
        }

        wp_redirect($url);
        exit;
    }

    public function check_current_page($template)
    {
        $code = sanitize_text_field($_GET['beautyalist-login']);
        if (is_front_page() && 'confirm-account' == $code) {
            if ($this->storage->get_data()) {
                return $template;
            }

            $instance = new API($this->client_id, $this->api_key);
            $data     = $instance->getToken($code);
            if ($data && !empty($data['access_token'])) {
                $user_info = $instance->getUserData($data['access_token']);
                if ($user_info) {
                    $service = new Service();
                    $res     = $service->proceed_user($user_info, $this->role);
                    if (!$res['status']) {
                        if ('email_exist' == $res['error_code']) {
                            $this->storage->set_data($data['access_token'], $res['email']);
                        }
                    } else {
                        // wp_new_user_notification( $user_id, null,'both');
                        wp_set_auth_cookie($res['user']->id, false, is_ssl());
                        wp_redirect($this->page);
                    }
                }
            }
        }

        return $template;
    }

    public function insert_header_code()
    {
        if (!empty($_GET['beautyalist-login'])) {
            $code = sanitize_text_field($_GET['beautyalist-login']);
            if (is_front_page() && 'confirm-account' == $code) {
                if ($this->storage->get_data()) {
                    $service = new ViewService();
                    $service->get_popup();
                }
            }
        }
    }

    public function handle_bl_login_login()
    {
        $result  = ['success' => false];
        $error   = false;
        $message = '';
        $service = new Service();

        if (!array_key_exists('nonce', $_POST) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'bl-login-login')) {
            $message = 'Wrong nonce key. Please refresh page and try again!';
            $error   = true;
        }

        if (!check_ajax_referer('bl-login-login', 'nonce')) {
            $message = 'Wrong referer. Please refresh page and try again!';
            $error   = true;
        }

        $username = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : null;
        $password = isset($_POST['pass']) ? sanitize_text_field($_POST['pass']) : null;

        if (empty($username) || empty($password)) {
            $message = 'Please provide email address and password!';
            $error   = true;
        }

        if (!$error) {
            $user = wp_authenticate($username, $password);
            if (is_wp_error($user)) {
                $message = $user->get_error_message();
                $code    = $user->get_error_code();
                if ('incorrect_password' == $code) {
                    $open    = '<strong>';
                    $close   = '</strong>';
                    $error   = '%sError:%s The password you entered for the email address %s%s%s is incorrect.';
                    $message = ''.sprintf($error, $open, $close, $open, $username, $close);
                }
                $error = true;
            } else {
                if ($data = $this->storage->get_data()) {
                    $instance  = new API($this->client_id, $this->api_key);
                    if ($data['email'] == $user->user_email) {
                        $user_info = $instance->getUserData($data['token']);
                        update_user_meta($user->ID, $this->match_option_name, $user_info['email']);
                        update_user_meta($user->ID, $this->licences_option_name, $service->prepare_licences($user_info['licences']));
                        update_user_meta($user->ID, $this->tag_option_name, $this->default_tag);

                        $this->storage->drop_data();

                        wp_set_current_user($user->ID, $user->user_login);
                        wp_set_auth_cookie($user->ID);
                        do_action('wp_login', $user->user_login, $user);

                        $result['success'] = true;
                    } else {
                        $message = 'Access denied';
                    }
                } else {
                    wp_set_current_user($user->ID, $user->user_login);
                    wp_set_auth_cookie($user->ID);
                    do_action('wp_login', $user->user_login, $user);

                    $result['success'] = true;
                }
            }
        }

        if ($error) {
            $result['message'] = $message;
        }

        wp_send_json($result);
    }

    public function extra_profile_fields($user)
    {
        if (current_user_can('administrator')) {
            $licenses   = esc_attr(get_the_author_meta($this->licences_option_name, $user->ID));
            $tag        = esc_attr(get_the_author_meta($this->tag_option_name, $user->ID));
            $service    = new ViewService();
            $service->get_profile_field($licenses, $tag);
        }
    }

    public function save_extra_profile_fields($user_id)
    {
        if (!array_key_exists('bll_tag_nonce', $_POST) || !wp_verify_nonce(sanitize_text_field($_POST['bll_tag_nonce']), 'bl-login-tag-nonce')) {
            return false;
        }

        if (current_user_can('administrator') && current_user_can('edit_user', $user_id)) {
            update_user_meta($user_id, $this->tag_option_name, esc_attr(sanitize_text_field($_POST['bll_tag'])));
        }
    }
}
