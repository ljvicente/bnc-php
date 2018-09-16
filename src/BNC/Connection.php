<?php

namespace ljvicente\BNC;

/**
 * Connection handler for all BNC endpoints starts here.
 *
 * @author Leo <jemnuineuron@gmail.com>
 */
class Connection
{
    const API_BASE = 'https://air.bncnetwork.net/api';

    private $token = '';

    /**
     * Automatically fetch login tokens.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $result = $this->post('/auth/user/login_token/', [
            'username' => $username,
            'password' => $password,
        ]);
        $this->token = $result['token'];
    }

    /**
     * Set GET request via cURL.
     *
     * @param string $endpoint
     * @return array
     */
    public function get($endpoint)
    {
        $ch = curl_init();
        
        $options = [
            CURLOPT_URL => self::API_BASE . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => [
                'authorization: Token ' . $this->token,
                'accept: application/json',
                'accept-encoding: gzip, deflate, br',
                'accept-language: en-US,en;q=0.9',
                'cache-control: no-cache',
                'origin: https://air.bncnetwork.net',
                'pragma: no-cache',
                'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36'
            ],
        ];
        curl_setopt_array($ch, $options);
        
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    /**
     * Send POST request via cURL.
     *
     * @param string $endpoint
     * @param array $content
     * @return array
     */
    public function post($endpoint, $content)
    {
        $ch = curl_init();
        curl_setopt_array(
            $ch,
            [
                CURLOPT_URL => self::API_BASE . $endpoint,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => http_build_query($content),
            ]
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }
}
