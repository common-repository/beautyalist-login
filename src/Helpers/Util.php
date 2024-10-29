<?php

namespace BeautyAListLogin\Helpers;

class Util
{
    public function prepare_params()
    {
        $args   = [];
        $params = ['code'];
        foreach ($params as $param) {
            $args[$param] = [
                'required'          => false,
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ];
        }

        return $args;
    }

    public static function get_system_page()
    {
        global $wp;

        return home_url($wp->request).'/wp-json/beautyalist_login/v1/callback';
    }

    public static function get_roles()
    {
        global $wp_roles;

        $all_roles      = $wp_roles->roles;
        $editable_roles = apply_filters('editable_roles', $all_roles);

        return $editable_roles;
    }

    public static function set_options()
    {
        $apikey_option_name           = 'bl_login_api_key';
        $client_id_option_name        = 'bl_login_client_id';
        $page_option_name             = 'bl_login_page';
        $role_option_name             = 'bl_role';

        if (!$api_key = get_option($apikey_option_name)) {
            $api_key = '';
        }

        if (!$client_id = get_option($client_id_option_name)) {
            $client_id = '';
        }

        if (!$page = get_option($page_option_name)) {
            $page = '';
        }

        if (!$role = get_option($role_option_name)) {
            $role = 'subscriber';
        }

        return [$api_key, $client_id, $page, $role];
    }
}
