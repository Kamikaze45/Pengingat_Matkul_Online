<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected string $token;
    protected string $endpoint;

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN');
        $this->endpoint = env('FONNTE_ENDPOINT', 'https://api.fonnte.com/send');
    }

    public function send(string $target, string $message)
    {
        // ========== NORMALISASI NOMOR ==========
        $original = $target;

        $target = trim($target);
        $target = ltrim($target, '+');
        $target = preg_replace('/^0/', '62', $target);

        // ========== INISIALISASI CURL ==========
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'target' => $target,
                'message' => $message,
            ],
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->token,
            ],

            // ========== FIX SSL ERROR ==========
            // Pastikan path ke cacert.pem benar!
            CURLOPT_CAINFO => 'C:\\xampp\\php\\extras\\ssl\\cacert.pem',

            // Tambahan biar error lebih jelas
            CURLOPT_VERBOSE => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        // ========== LOGGING ==========
        Log::info("WA dikirim", [
            'original_target'   => $original,
            'normalized_target' => $target,
            'http_code'         => $httpCode,
            'api_response'      => $response,
            'error'             => $error,
        ]);

        return $response;
    }
}
