<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirebaseNotificationService
{
    protected $credentials;

    public function __construct()
    {
        $this->credentials = json_decode(
            file_get_contents(storage_path('app/firebase/firebase_credentials.json')),
            true
        );
    }

public function send($tokens, $title, $body, $data = [])
{
    $accessToken = $this->getAccessToken();

    foreach ($tokens as $token) {
        $payloadData = array_merge([
            "title" => (string) $title,
            "body" => (string) $body,
        ], array_map('strval', $data));

        $response = Http::withToken($accessToken)
            ->post("https://fcm.googleapis.com/v1/projects/{$this->credentials['project_id']}/messages:send", [
                "message" => [
                    "token" => $token,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                    ],
                    "data" => $payloadData,
                ],
            ]);

        logger()->info('Firebase response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
    }
}

    private function getAccessToken()
    {
        $jwt = $this->createJwt();

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response['access_token'];
    }

    private function createJwt()
    {
        $header = base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]));

        $now = time();

        $payload = base64_encode(json_encode([
            'iss' => $this->credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $signatureInput = "$header.$payload";

        openssl_sign(
            $signatureInput,
            $signature,
            $this->credentials['private_key'],
            'SHA256'
        );

        return $header . '.' . $payload . '.' . base64_encode($signature);
    }
}