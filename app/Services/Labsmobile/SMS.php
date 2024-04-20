<?php

namespace App\Services\Labsmobile;

use App\Models\Setting;

class SMS
{
    public static function send(array $to = [], string $message = '')
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

            $count = Setting::firstOrCreate(
                ['key' => 'sms_count'],
                [
                    'value' => 0,
                    'description' => 'Cantidad de mensajes enviados'
                ]
            );

            $count->value = $count->value + count($to);
            $count->save();

            return json_decode($response)->message;
        }
    }

    public static function balance()
    {
        $auth_basic = base64_encode(config('services.labsmobile.username') . ':' . config('services.labsmobile.password'));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.labsmobile.com/json/balance",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic " . $auth_basic,
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "Error al obtener el balance: " . $err;
        } else {
            return json_decode($response)->credits;
        }
    }

    public static function prices()
    {
        $auth_basic = base64_encode(config('services.labsmobile.username') . ':' . config('services.labsmobile.password'));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.labsmobile.com/json/prices",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{"format":"JSON","countries":["CO"]}',
            CURLOPT_HTTPHEADER => array(
              "Authorization: Basic ".$auth_basic,
              "Cache-Control: no-cache",
              "Content-Type: application/json",
            ),
          ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "Error al obtener los precios: " . $err;
        } else {
            return json_decode($response)->CO->credits;
        }
    }
}
