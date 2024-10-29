<?php

namespace BeautyAListLogin\Services;

use Beautyalist\API as API_BL;
use Beautyalist\Client\CurlClient;

class API extends API_BL
{
    public function check_data($page)
    {
        $data = [];
        $url  = '/openid/v1.1/check';
        $curl = new CurlClient();

        $headers = ['Content-type:application/json'];
        $params  = [
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_url'  => $page,
        ];

        $result = $curl->startRequest('post', self::URL.$url, $headers, $params);
        if ($result['code'] && 200 == $result['code']) {
            if (!empty($result['body'])) {
                $data = \json_decode($result['body'], true);
            }
        } else {
            if ($result['body']) {
                $body = [];
                try {
                    $body = json_decode($result['body'], true);
                } catch (\Exception $e) {
                }

                if ($body['message']) {
                    $data['message'] = $body['message'];
                }
            }

            $data['status'] = false;
        }

        return $data;
    }
}
