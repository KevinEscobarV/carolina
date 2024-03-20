<?php

namespace App\Services\Labsmobile;

class SMS
{
    public function send(array $to = [], string $message = '')
    {
        $auth_basic = base64_encode(config('services.labsmobile.username') . ':' . config('services.labsmobile.password'));

        $curl = curl_init();

        $recipient = [];

        foreach ($to as $phone) {
            $recipient[] = ['msisdn' => $phone];
        }

        $body = array(
            'message' => $message,
            'recipient' => $recipient
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.labsmobile.com/json/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic " . $auth_basic,
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "Error al enviar el mensaje: " . $err;
        } else {
            return $response;
        }
    }
}
