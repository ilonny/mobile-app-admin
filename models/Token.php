<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "token".
 *
 * @property int $id
 * @property string $token
 * @property string $settings
 * @property string $other
 */
class Token extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token', 'settings'], 'required'],
            [['token', 'settings', 'other'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'token' => 'Token',
            'settings' => 'Settings',
            'other' => 'Other',
        ];
    }

    public function sendPushForGroup($setting, $payload_text){
        // where(['or', ['like','settings', "$setting,"], ['like','settings', ",$setting]"]])->all();
        $models = Token::find()
        ->where([
            'or',
            ['like','settings', ",$setting,"],
            ['like','settings', ",$setting]"],
            ['like','settings', "[$setting,"],
            ['=','settings', "all"],
        ])
        ->all();
        $tokens = [];
        foreach ($models as $model){
            $tokens[] = $model->other;
        }
        function replace_unicode_escape_sequence($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }
        function send_ios_push($message, $tokens) {
            $sound = 'default';
            $development = false;
            $payload = array();
            $payload["aps"] = array('alert' => $message, 'sound' => $sound);
            $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);
            $payload = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $payload);
            $apns_url = NULL;
            $apns_cert = NULL;
            $apns_port = 2195;
        
            if($development) {
                $apns_url = 'gateway.sandbox.push.apple.com';
                $apns_cert = 'apns-dev.pem';
            } else {
                $apns_url = 'gateway.push.apple.com';
                $apns_cert = 'apns-prod.pem';
            }
            
            if (!file_exists($apns_cert)) 
            // echo("cert file exists\n"); 
            // else 
            echo("cert file not exists\n");; 
            $success = 0;
            $stream_context = stream_context_create();
            stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
            stream_context_set_option($stream_context, 'ssl', 'passphrase', 'Rh3xwaex9g');
        
            $apns = stream_socket_client('ssl://' . $apns_url . ':' . $apns_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);
            
            $device_tokens = $tokens;
            
            foreach($device_tokens as $device) {
                $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device)) . chr(0) . chr(strlen($payload)) . $payload;
                // var_dump($apns_message);die();
                if (fwrite($apns, $apns_message)) {
                    $success++;
                    // echo("sent\n");
                } else {
                    echo("failed \n");
                }
            }
            // echo("fetch done\n"); 
            socket_close($apns);
            fclose($apns);
            return $success;
        }
        
        send_ios_push($payload_text, $tokens);

    }

    public function sendPushForAll($payload_text){
        $models = Token::find()->all();
        $tokens = [];
        foreach ($models as $model){
            $tokens[] = $model->other;
        }
        function replace_unicode_escape_sequence($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }
        function send_ios_push($message, $tokens) {
            $sound = 'default';
            $development = false;
            $payload = array();
            $payload["aps"] = array('alert' => $message, 'sound' => $sound);
            $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);
            $payload = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $payload);
            $apns_url = NULL;
            $apns_cert = NULL;
            $apns_port = 2195;
            // var_dump($payload);die();
            if($development) {
                $apns_url = 'gateway.sandbox.push.apple.com';
                $apns_cert = 'apns-dev.pem';
            } else {
                $apns_url = 'gateway.push.apple.com';
                $apns_cert = 'apns-prod.pem';
            }
            
            if (!file_exists($apns_cert)) 
            // echo("cert file exists\n"); 
            // else 
            echo("cert file not exists\n");; 
            $success = 0;
            $stream_context = stream_context_create();
            stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
            stream_context_set_option($stream_context, 'ssl', 'passphrase', 'Rh3xwaex9g');
        
            $apns = stream_socket_client('ssl://' . $apns_url . ':' . $apns_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);
            
            $device_tokens = $tokens;
            
            foreach($device_tokens as $device) {
                $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device)) . chr(0) . chr(strlen($payload)) . $payload;
                // var_dump($apns_message);die();
                if (fwrite($apns, $apns_message)) {
                    $success++;
                    // echo("sent\n");
                } else {
                    echo("failed \n");
                }
            }
            // echo("fetch done\n"); 
            socket_close($apns);
            fclose($apns);
            return $success;
        }
        
        send_ios_push($payload_text, $tokens);

    }

}
