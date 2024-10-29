<?php

namespace BeautyAListLogin\Services;

class Service
{
    private static string $page_option_name    = 'bl_login_page';
    private string $match_option_name          = 'bl_login_match';
    private string $licences_option_name       = 'bl_login_licences';
    private string $tag_option_name            = 'bl_login_tag';

    private string $default_tag                = 'beautyalist';

    public function prepare_licences($value)
    {
        $s = '';
        $r = "\r\n";
        $c = 1;
        foreach ($value as $item) {
            $s .= 'Licence #'.$c++.$r;
            $s .= 'number: '.$item['number'].$r;
            $s .= 'state: '.$item['state'].$r;
            $s .= 'expired date: '.$item['expired_date'].$r;
            $s .= $r;
        }

        return $s;
    }

    public function proceed_user($data, $role)
    {
        $res          = ['status' => false];
        $email        = strtolower($data['email']);
        $search_data  = ['meta_key' => $this->match_option_name, 'meta_value' => $email, 'number' => 1];
        $users        = get_users($search_data);
        if (!$users) {
            // create user
            $username_part = explode('@', $email);
            $username      = strtolower(sanitize_text_field($username_part[0]));
            $username      = sanitize_user($username, true);

            $data_new = [
                'username'   => $this->generate_unique_username($username),
                'password'   => wp_generate_password(12, true),
                'email'      => $email,
                'firstname'  => $data['first_name'],
                'lastname'   => $data['last_name'],
            ];

            if (email_exists($email)) {
                // show email error here
                $res['error_code'] = 'email_exist';
                $res['email']      = $email;
            } else {
                $user_id = wp_create_user($data_new['username'], $data_new['password'], $email);
                $user    = new \WP_User($user_id);
                $user->set_role($role);

                update_user_meta($user_id, 'first_name', $data_new['firstname']);
                update_user_meta($user_id, 'last_name', $data_new['lastname']);

                update_user_meta($user_id, $this->match_option_name, $data_new['email']);
                update_user_meta($user_id, $this->licences_option_name, $this->prepare_licences($data['licences']));
                update_user_meta($user_id, $this->tag_option_name, $this->default_tag);

                // start hook
                do_action('beautyalist_login_create_user', $user_id, $data);

                $res = ['status' => true, 'user' => $user];
            }
        } else {
            $res = ['status' => true, 'user' => reset($users)];
        }

        return $res;
    }

    public function generate_unique_username($username)
    {
        $username = sanitize_user($username);

        static $i;
        if (null === $i) {
            $i = 1;
        } else {
            ++$i;
        }
        if (!username_exists($username)) {
            return $username;
        }
        $new_username = sprintf('%s-%s', $username, $i);
        if (!username_exists($new_username)) {
            return $new_username;
        }

        return call_user_func(__FUNCTION__, $username);
    }

    public function create_page($id, $page, $title)
    {
        $res              = ['status' => false];
        $service          = new ViewService();
        $page_content     = $service->generate_new_page($id, $page);
        $check_page_exist = $this->check_page($title);
        if (!$check_page_exist) {
            $data             = [
                'comment_status' => 'close',
                'ping_status'    => 'close',
                'post_author'    => 1,
                'post_title'     => ucwords($title),
                'post_name'      => strtolower(str_replace(' ', '-', trim($title))),
                'post_status'    => 'publish',
                'post_content'   => $page_content,
                'post_type'      => 'page',
                'post_parent'    => '',
            ];
            $page_id = wp_insert_post($data);
            if (get_option(self::$page_option_name)) {
                delete_option(self::$page_option_name);
            }
            add_option(self::$page_option_name, $page_id.'', '', 'no');

            $res = ['status' => true];
        } else {
            $res['message'] = 'Page with this title exist. Please change title and try again.';
        }

        return $res;
    }

    private function check_page($title)
    {
        $page              = false;
        $query             = new \WP_Query(
            [
                'post_type'              => 'page',
                'title'                  => $title,
                'posts_per_page'         => 1,
                'no_found_rows'          => true,
                'ignore_sticky_posts'    => true,
                'update_post_term_cache' => false,
                'update_post_meta_cache' => false,
            ]
        );

        if (!empty($query->post)) {
            $page = true;
        }

        return $page;
    }
}
