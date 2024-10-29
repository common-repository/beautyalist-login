<?php

namespace BeautyAListLogin\Controllers;

use BeautyAListLogin\Helpers\Util;
use BeautyAListLogin\Services\API;
use BeautyAListLogin\Services\Service;
use BeautyAListLogin\Services\ViewService;

class Settings
{
    private string $apikey_option_name    = 'bl_login_api_key';
    private string $client_id_option_name = 'bl_login_client_id';
    private string $page_option_name      = 'bl_login_page';
    private string $role_option_name      = 'bl_role';

    private string $api_key          = '';
    private string $client_id        = '';
    private string $page             = '';
    private string $role             = '';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_menus']);
        add_action('admin_footer', [$this, 'insert_header_code']);
        add_action('admin_enqueue_scripts', [$this, 'add_assets']);
        add_action('admin_post_bl_login_save_key', [$this, 'handle_bl_login_save_data']);
        add_action('admin_post_bl_login_save_role', [$this, 'handle_bl_login_save_role']);
        add_action('admin_post_bl_login_create_page', [$this, 'handle_bl_login_create_page']);

        list($this->api_key, $this->client_id, $this->page, $this->role) = Util::set_options();
    }

    public function handle_bl_login_create_page()
    {
        if (!current_user_can('manage_options')) {
            return wp_send_json(['status' => 'Access denied!']);
        }

        if (!array_key_exists('nonce', $_POST) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'bl-login-create-page-nonce')) {
            return wp_send_json(['status' => 'Access denied!']);
        }

        if (!array_key_exists('action', $_POST) || 'bl_login_create_page' != sanitize_text_field($_POST['action'])) {
            return wp_send_json(['status' => 'Access denied!']);
        }

        $result = ['success' => false, 'message' => '<p>Provide page name!</p>'];

        $page   = trim(sanitize_text_field($_POST['page']));
        if ($page) {
            $service = new Service();
            $res     = $service->create_page($this->client_id, Util::get_system_page(), $page);
            if ($res['status']) {
                $result = ['success' => true];
            } else {
                $result['message'] = '<p>'.$res['message'].'<p>';
            }
        }

        wp_send_json($result);
    }

    public function insert_header_code()
    {
        if (!empty($_GET['page']) && in_array(sanitize_text_field($_GET['page']), ['beautyalist-login-settings'])) {
            $service = new ViewService();
            $service->get_page();
        }
    }

    public function handle_bl_login_save_data()
    {
        if (!current_user_can('manage_options')) {
            return wp_send_json(['status' => 'Access denied!']);
        }

        if (!array_key_exists('nonce', $_POST) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'bl-login-save-key-nonce')) {
            return wp_send_json(['status' => 'Access denied!']);
        }

        if (!array_key_exists('action', $_POST) || 'bl_login_save_key' != sanitize_text_field($_POST['action'])) {
            return wp_send_json(['status' => 'Access denied!']);
        }

        $result = ['success' => true, 'message' => '<p class="text-success">Data saved!</p>'];

        $key   = trim(sanitize_text_field($_POST['key']));
        $id    = trim(sanitize_text_field($_POST['id']));

        $api = new API($id, $key);
        $res = $api->check_data(Util::get_system_page());
        if (!$res['status']) {
            $result['success'] = false;
            $result['message'] = '<p class="text-danger">Invalid data environment specified.</p>'.
                '<p>Please enter correct ClientID and API key or contact '.
                '<a href="mailto:info@beauticianlist.com">info@beauticianlist.com</a></p>';
        } else {
            update_option($this->apikey_option_name, $key);
            update_option($this->client_id_option_name, $id);
        }

        wp_send_json($result);
    }

    public function handle_bl_login_save_role()
    {
        if (!current_user_can('manage_options')) {
            return wp_send_json(['status' => 'Access denied!']);
        }

        if (!array_key_exists('nonce', $_POST) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'bl-login-save-role-nonce')) {
            return wp_send_json(['status' => 'Access denied!']);
        }

        if (!array_key_exists('action', $_POST) || 'bl_login_save_role' != sanitize_text_field($_POST['action'])) {
            return wp_send_json(['status' => 'Access denied!']);
        }

        $result = ['success' => true, 'message' => '<p class="text-success">Data saved!</p>'];

        update_option($this->role_option_name, trim(sanitize_text_field($_POST['role'])));

        wp_send_json($result);
    }

    public function add_assets()
    {
        $version = '1.0.3';

        if (!empty($_GET['page']) && in_array(sanitize_text_field($_GET['page']), ['beautyalist-login-settings'])) {
            $dir = plugin_dir_url(__FILE__).'../../assets/';

            wp_enqueue_script('bl-plugin-script', $dir.'js/script.js', ['jquery'], $version, true);
            wp_enqueue_script('bl-plugin-script-bootstrap', $dir.'js/vendors/bootstrap.bundle.min.js', [], $version, true);

            wp_enqueue_style('bl-plugin-style-bootstrap-io', $dir.'css/bootstrap-iso.css', false, $version);
            wp_enqueue_style('bl-plugin-style', $dir.'css/styles.css', false, $version);
            wp_enqueue_style('bl-plugin-style-overlay', $dir.'css/overlay.css', false, $version);
        }
    }

    public function initializer()
    {
        return [
            'beautyalist-login-settings' => [
                'page_title' => 'BeautyAList Login',
                'menu_title' => 'BeautyAList Login',
                'capability' => 'administrator',
                'function'   => [$this, 'menu'],
                'icon_url'   => '',
                'priority'   => 90,
            ],
        ];
    }

    public function menu()
    {
        $service        = new ViewService();
        $settings       = ['error' => false];
        $api            = new API($this->client_id, $this->api_key);
        $res            = $api->check_data(Util::get_system_page());
        if (!$res['status']) {
            $settings = ['error' => true];
            if ($res['message']) {
                $settings['message'] = $res['message'];
            }
        }

        $service->get_admin_page(
            $this->api_key,
            $this->client_id,
            $this->page,
            $settings,
            Util::get_system_page(),
            $this->role,
            Util::get_roles(),
        );
    }

    public function add_menus()
    {
        foreach ($this->initializer() as $slug => $menu) {
            add_menu_page($menu['page_title'], $menu['menu_title'], $menu['capability'], $slug, $menu['function'], $menu['icon_url'], $menu['priority']);
        }
    }
}