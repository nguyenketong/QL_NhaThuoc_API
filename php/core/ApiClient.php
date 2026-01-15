<?php
/**
 * API Client - Để Website gọi RESTful API
 */
class ApiClient
{
    private static $baseUrl;
    private static $token;

    public static function init()
    {
        // Gọi API nội bộ
        self::$baseUrl = BASE_URL . '/api';
        self::$token = $_SESSION['api_token'] ?? null;
    }

    public static function setToken($token)
    {
        self::$token = $token;
        $_SESSION['api_token'] = $token;
    }

    public static function getToken()
    {
        return self::$token;
    }

    public static function clearToken()
    {
        self::$token = null;
        unset($_SESSION['api_token']);
    }

    /**
     * GET request
     */
    public static function get($endpoint, $params = [])
    {
        $url = self::$baseUrl . '/' . ltrim($endpoint, '/');
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
        return self::request('GET', $url);
    }

    /**
     * POST request
     */
    public static function post($endpoint, $data = [])
    {
        $url = self::$baseUrl . '/' . ltrim($endpoint, '/');
        return self::request('POST', $url, $data);
    }

    /**
     * PUT request
     */
    public static function put($endpoint, $data = [])
    {
        $url = self::$baseUrl . '/' . ltrim($endpoint, '/');
        return self::request('PUT', $url, $data);
    }

    /**
     * DELETE request
     */
    public static function delete($endpoint)
    {
        $url = self::$baseUrl . '/' . ltrim($endpoint, '/');
        return self::request('DELETE', $url);
    }

    /**
     * Thực hiện HTTP request
     */
    private static function request($method, $url, $data = null)
    {
        $ch = curl_init();

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        if (self::$token) {
            $headers[] = 'Authorization: Bearer ' . self::$token;
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $error,
                'error_code' => 0
            ];
        }

        $result = json_decode($response, true);
        
        if (!$result) {
            return [
                'success' => false,
                'message' => 'Invalid response',
                'error_code' => $httpCode
            ];
        }

        return $result;
    }
}
