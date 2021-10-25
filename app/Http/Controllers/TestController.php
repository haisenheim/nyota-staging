<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mail;

class TestController extends Controller
{
    public function index()
    {
		$src = '<?xml version="1.0" encoding="UTF-8"?>';
        $src = $src . "<SMS> 
            <operations>  
                <operation>SEND</operation> 
            </operations> 
            <authentification>    
                <username>jesusnaissant@gmail.com</username>   
                <password>Isis@2014</password>   
            </authentification>   
            <message> 
                <sender>SMS</sender>    
                <text>testing</text>   
            </message> 
			<numbers>	
            <number>91997849512</number>    
			</numbers>
        </SMS>";

        $curl = curl_init();
        $curlOptions = array(
            CURLOPT_URL => 'http://api.atompark.com/members/sms/xml.php',
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_POST => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 100,
            CURLOPT_POSTFIELDS => array('XML' => $src),
        );
        curl_setopt_array($curl, $curlOptions);
        if (false === ($xmlString = curl_exec($curl))) {
            throw new Exception('Http request failed');
        }
        curl_close($curl);

        $xml = simplexml_load_string($xmlString);
        $json = json_encode($xml);
        $result = json_decode($json, 1);
		
		print_r($result);
		
		
    }

    public function emailtest(){
        $data['orderid'] = '12';
        $email = 'raj@icreativetechnologies.com';
        Mail::send('emails.adminorder',['data' => $data], function($message) use($email)
        {
            $message->to($email, 'Nyotaapp')->subject('New Order Placed');
        });
    }

}