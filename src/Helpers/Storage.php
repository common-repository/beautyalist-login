<?php

namespace BeautyAListLogin\Helpers;

class Storage
{
    private $cookie_title = 'bl_id';
    private $param_title  = 'bl_login_access_token';
    private $id           = '';

    public function get_id()
    {
        if (empty($this->id)) {
            $token    = wp_generate_uuid4();
            $this->id = str_replace('-', '', $token);
            setcookie($this->cookie_title, $this->id, time() + 3600 * 12, COOKIEPATH, COOKIE_DOMAIN, false);
        }

        return $this->id;
    }

    public function get_data()
    {
        $res = false;
        if (!empty($_COOKIE['bl_id'])) {
            $data = get_transient($this->param_title.'_'.strip_tags($_COOKIE['bl_id']));
            if ($data) {
                $decrypt = unserialize($data);
                $res     = ['token' => @sanitize_text_field($decrypt['token']), 'email' => $decrypt['email']];
            }
        }

        return $res;
    }

    public function set_data($token, $email)
    {
        $data = [
            'token' => sanitize_text_field($token),
            'email' => $email,
        ];

        return set_transient($this->param_title.'_'.$this->get_id(), serialize($data), 12 * 60 * 60);
    }

    public function drop_data()
    {
        if (!empty($_COOKIE[$this->cookie_title])) {
            delete_transient($this->param_title.'_'.strip_tags($_COOKIE['bl_id']));
            unset($_COOKIE[$this->cookie_title]);
            setcookie($this->cookie_title, '', time() - 3600);
        }
    }
}
