<?php

namespace App\Services;

use App\Models\Cheif;
use App\Models\User;
use App\Notifications\FcmDatabaseNotification;
use App\Responses\responseApi;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;

class FcmNotificationService
{
    use responseApi;
    public function sendNotificationToUser($userId, $title, $body)
    {
        $cheif = Cheif::find($userId);

        if (!$cheif) {
            return $this->responseError('User not found', 400);
        }

        $fcmToken = $cheif->fcm_token;

        if (!$fcmToken) {
            return $this->responseError('User does not have a device token', 400);
        }

        // Create notification data
        $notificationData = [
            'title' => $title,
            'body' => $body,
            'type' => 'fcm_notification',
            'time' => date('Y-m-d H:i:s')
        ];

        // Send FCM notification
        $fcmResponse = $this->sendFirebaseNotification($fcmToken, $title, $body);

        // Save to database
        $cheif->notify(new FcmDatabaseNotification($notificationData));

        return [
            'fcm_response' => $fcmResponse,
            'notification' => $notificationData
        ];
    }

    protected function sendFirebaseNotification($fcmToken, $title, $body)
    {
        $projectId = config('services.fcm.project_id');
        $credentialsFilePath = Storage::path('app/json/round3-restaurant-app-firebase.json');

        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $headers = [
            "Authorization: Bearer {$token['access_token']}",
            'Content-Type: application/json'
        ];

        $data = [
            "message" => [
                "token" => $fcmToken,
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                ],
            ]
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            throw new \Exception('FCM Curl Error: ' . $err);
        }

        return json_decode($response, true);
    }
}
