<?php

namespace app\models;

use Yii;
use yii\httpclient\Client;
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
        $models = Token::find()
        ->where([
            'or',
            ['like','settings', ",$setting,"],
            ['like','settings', ",$setting]"],
            ['like','settings', "[$setting,"],
            ['=','settings', "all"],
        ])
        ->all();
        $tokens_ios = [];
        $tokens_android = [];
        foreach ($models as $model){
            $token_obj = json_decode($model->token);
            if ($token_obj->os == 'ios'){
                $tokens_ios[] = $model->other;
            } else {
                $tokens_android[] = $model->other;
            }
        }
            $message = $payload_text;

            $sound = 'default';
            $development = false;
            $payload = array();
            $payload["aps"] = array('alert' => $message, 'sound' => $sound);
            $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);
            // $payload = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $payload);
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

            $device_tokens = $tokens_ios;

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

    public function sendPushForGroupAndroid($setting, $payload_text){
        $models = Token::find()
        ->where([
            'or',
            ['like','settings', ",$setting,"],
            ['like','settings', ",$setting]"],
            ['like','settings', "[$setting,"],
            ['=','settings', "all"],
        ])
        ->all();
        $tokens_ios = [];
        $tokens_android = [];
        foreach ($models as $model){
            $token_obj = json_decode($model->token);
            if ($token_obj->os == 'ios'){
                $tokens_ios[] = $model->other;
            } else {
                $tokens_android[] = $model->other;
            }
        }

        foreach ($tokens_android as $android_token){
            $android_push_body = array(
                'to' => $android_token,
                'data' => array(
                    'body' => $payload_text
                    )
            );
            $android_push_body = json_encode($android_push_body, JSON_UNESCAPED_UNICODE);
            $ch = curl_init('https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $android_push_body);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: key=AAAAmLg0GRc:APA91bGaOgw6-8zB6Q_o7A-Qf5BU7ofEQqM5UoMAgIySYgcFQ3aS1z9V9W-Wk9Xa9qRrqaQ47qfo7tzAi4uY-4IzgAPpesbwVOYZQ4QX94VFCQvGLpSS4qaOwJpritlwf-n7BWsvH5jO9sKZAyA56vdcL1Gt1mlKtg'
            ));
            $response = curl_exec($ch);
        }
        // var_dump($response);
        file_put_contents('debug.txt', json_encode($response), FILE_APPEND);
    }

    public function sendPushForAll($payload_text){ 
        //ios push for all 
        $models = Token::find()->all();
        $tokens_ios = [];
        $tokens_android = [];
        foreach ($models as $model){
            $token_obj = json_decode($model->token);
            if ($token_obj->os == 'ios'){
                $tokens_ios[] = $model->other;
            } else {
                $tokens_android[] = $model->other;
            }
        }
            $message = $payload_text;
            $sound = 'default';
            $development = false;
            $payload = array();
            $payload["aps"] = array('alert' => $message, 'sound' => $sound);
            $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);
            // $payload = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $payload);
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

            $device_tokens = $tokens_ios;

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

    public function sendPushForAllAndroid($payload_text){
        $models = Token::find()->all();
        $tokens_ios = [];
        $tokens_android = [];
        foreach ($models as $model){
            $token_obj = json_decode($model->token);
            if ($token_obj->os == 'ios'){
                $tokens_ios[] = $model->other;
            } else {
                $tokens_android[] = $model->other;
            }
        }

        foreach ($tokens_android as $android_token){
            $android_push_body = array(
                'to' => $android_token,
                'data' => array(
                    'body' => $payload_text
                    )
            );
            $android_push_body = json_encode($android_push_body, JSON_UNESCAPED_UNICODE);
            $ch = curl_init('https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $android_push_body);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: key=AAAAmLg0GRc:APA91bGaOgw6-8zB6Q_o7A-Qf5BU7ofEQqM5UoMAgIySYgcFQ3aS1z9V9W-Wk9Xa9qRrqaQ47qfo7tzAi4uY-4IzgAPpesbwVOYZQ4QX94VFCQvGLpSS4qaOwJpritlwf-n7BWsvH5jO9sKZAyA56vdcL1Gt1mlKtg'
            ));
            $response = curl_exec($ch);
        }
        file_put_contents('debug.txt', json_encode($response), FILE_APPEND);  
    }
}
