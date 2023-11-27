<?php

declare(strict_types=1);

namespace Service;

class HTTP
{
    public static function get(string $url, array $headers = []): array
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);

        var_dump($info['header_size']);

        curl_close($curl);

        return [
            "response" => $response,
            "info" => $info
        ];
    }

    public static function post(string $url, array $headers = [], array $data = []): array
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);

        return [
            "response" => json_decode($response),
            "info" => $info
        ];
    }
}