<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (! function_exists('curl')):
    function curl($link, $method=null, $params=null, $authorization=null, $jsontype=false) {        
        $header= null;

		switch (strtoupper($method)) {
            case 'POST':
                $defaults = array(
                    CURLOPT_URL => $link,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS => $params
                );
				break;
			
            case 'PUT':
                $defaults = array(
                    CURLOPT_URL => $link,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_POSTFIELDS => $params
                );
				break;
            case 'DELETE':
                $defaults = array(
                    CURLOPT_URL => $link,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'DELETE'
                );
                break;
            default:
                $defaults = array(
                    CURLOPT_URL => $link,
                    CURLOPT_RETURNTRANSFER => true
                );
                break;
        }

        if(!is_null($authorization)){
            if($jsontype){
                $header = array(
                    'Authorization: '.$authorization,
                    'Content-Type: application/json; charset=UTF-8;',
                    'Content-length: 0',
                );
            } elseif(!$jsontype){
                $header = array(
                    'Authorization:'.$authorization,
                    'Content-Type:application/json',
                    'Content-length:'.strlen($params),
                );
            }

            $defaults[CURLOPT_HTTPHEADER] = $header;
        }

		$ch = curl_init();
		curl_setopt_array($ch, $defaults);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result);
	}
endif;

if (! function_exists('persentase')):
    function persentase($nilai){
        $pattern = "/\./";
        if (preg_match($pattern, $nilai, $matches,PREG_OFFSET_CAPTURE)) {
            return $nilai;
        }
        else {
            return $nilai.".0";
        }
    }
endif;