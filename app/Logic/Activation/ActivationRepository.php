<?php

namespace App\Logic\Activation;

use App\Models\Activation;
use App\Models\User;
use App\Notifications\SendActivationEmail;
use App\Traits\CaptureIpTrait;
use Carbon\Carbon;

class ActivationRepository
{
    public function createTokenAndSendEmail(User $user)
    {
        $activations = Activation::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subHours(config('settings.timePeriod')))
            ->count();

        if ($activations >= config('settings.maxAttempts')) {
            return true;
        }

        //if user changed activated email to new one
        if ($user->activated) {
            $user->update([
                'activated' => false,
            ]);
        }

        // Create new Activation record for this user
        $activation = self::createNewActivationToken($user);

        // Send activation email notification
        self::sendNewActivationEmail($user, $activation->token);
    }

    public function createNewActivationToken(User $user)
    {
        $ipAddress = new CaptureIpTrait();
        $activation = new Activation();
        $activation->user_id = $user->id;
        $activation->token = str_random(64);
        $activation->ip_address = $ipAddress->getClientIp();
        $activation->save();
        
        $url = url('/activate/'.$activation->token);
        $new_url = $this->get_tiny_url("".$url);

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
                        <text>Please click on <a href='".$new_url."''>'".$new_url."'</a> to activate your account in Nyota. </text>   
                    </message> 
                    <numbers>   
                    <number>'".$user->phone."'</number>    
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
                return $activation;
    }

    public function sendNewActivationEmail(User $user, $token)
    {

        $user->notify(new SendActivationEmail($token));
    }

    public function deleteExpiredActivations()
    {
        Activation::where('created_at', '<=', Carbon::now()->subHours(72))->delete();
    }

    function get_tiny_url($url)  {  
    $ch = curl_init();  
    $timeout = 5;  
    curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
    $data = curl_exec($ch);  
    curl_close($ch);  
    return $data;  
    }
}
