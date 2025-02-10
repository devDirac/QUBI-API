<?php

namespace App\Utils;

use App\Http\Controllers\API\BaseController as BaseController;


class Encrypt extends BaseController
{

    public function encrypt($string, $sKey, $sIv)
    {
        $encrypt_method = "AES-256-CBC";
        $output = false;
        //HASH    
        $key = hash('sha256', $sKey);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $sIv), 0, 16);
        
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        return $output;
    }

    public function decrypt($string, $sKey, $sIv)
    {
        $encrypt_method = "AES-256-CBC";
        $output = false;

        //HASH    
        $key = hash('sha256', $sKey);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $sIv), 0, 16);


        $output = openssl_decrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        return $output;


       /*  $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        return $output; */

    }

}
