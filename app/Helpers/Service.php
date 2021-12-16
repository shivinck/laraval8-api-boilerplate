<?php

namespace App\Helpers;

class Service {

    public static function sendOTP ($mobile) {
        $api_key = env('TWO_FACTOR_KEY');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://2factor.in/API/V1/$api_key/SMS/$mobile/AUTOGEN",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
              "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return  false;
        } else {
            return json_decode($response);
        }
    }


    public static function verifyOTP ($verify_id, $otp) {
        $api_key = env('TWO_FACTOR_KEY');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://2factor.in/API/V1/$api_key/SMS/VERIFY/$verify_id/$otp",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
              "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return  false;
        } else {
            return json_decode($response);
        }
    }
}
