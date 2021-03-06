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
 * @property string $lang
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
            [['token', 'settings', 'other', 'error', 'city_push'], 'string'],
            [['lang'], 'string', 'max' => 255],
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
            'lang' => 'Lang',
            'error' => 'Error',
            'ecadash' => 'Ecadash',
            'city_push' => 'CityPush',
        ];
    }

    public function sendPushForGroup($setting, $payload_text, $quote_id = null, $quote_title = null){
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
            $payload["quote_id"] = $quote_id;
            $payload["aps"] = array('alert' => $message, 'sound' => $sound);
            $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);
            $device_tokens = $tokens_ios;
            foreach($device_tokens as $device) {
                $curl_query = "curl -d '${payload}' --cert /var/www/flames_user/data/www/mobile-app.flamesclient.ru/web/apns-prod.pem:Rh3xwaex9g -H \"apns-topic: org.reactjs.native.example.GuruOnline\" --http2  https://api.push.apple.com/3/device/${device}";
                shell_exec($curl_query);
            }
    }

    public function sendPushForGroupWithAction($payload_text){
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
        $payload["need_alert"] = true;
        $payload["aps"] = array('alert' => $message, 'sound' => $sound);
        $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $device_tokens = $tokens_ios;
        foreach($device_tokens as $device) {
            $curl_query = "curl -d '${payload}' --cert /var/www/flames_user/data/www/mobile-app.flamesclient.ru/web/apns-prod.pem:Rh3xwaex9g -H \"apns-topic: org.reactjs.native.example.GuruOnline\" --http2  https://api.push.apple.com/3/device/${device}";
            shell_exec($curl_query);
        }
    }

    public function sendPushForGroupAndroid($setting, $payload_text, $quote_id = null, $quote_title = null){
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
                    'body' => array(
                        'text' => $payload_text,
                        'q_id' => intval($quote_id),
                    ),
                    'title' => $quote_title
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

    public function sendPushForGroupAndroidWithAction($payload_text){
        $models = Token::find()
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
                    'body' => array(
                        'text' => $payload_text,
                        'need_alert' => true
                    ),
                    'title' => $quote_title
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
            $payload["aps"] = array('alert' => $message);
            $payload = json_encode($payload);
            $payload = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function ($match) {
                return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
              }, $payload);            
            // $apns_url = NULL;
            // $apns_cert = NULL;
            // $apns_port = 2195;

            // if($development) {
            //     $apns_url = 'gateway.sandbox.push.apple.com';
            //     $apns_cert = 'apns-dev.pem';
            // } else {
            //     $apns_url = 'gateway.push.apple.com';
            //     $apns_cert = 'apns-prod.pem';
            // }

            // if (!file_exists($apns_cert))
            // // echo("cert file exists\n");
            // // else
            // echo("cert file not exists\n");;
            // $success = 0;
            // $stream_context = stream_context_create();
            // stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
            // stream_context_set_option($stream_context, 'ssl', 'passphrase', 'Rh3xwaex9g');

            // $apns = stream_socket_client('ssl://' . $apns_url . ':' . $apns_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);

            $device_tokens = $tokens_ios;

            foreach($device_tokens as $device) {
                $curl_query = "curl -d '${payload}' --cert /var/www/flames_user/data/www/mobile-app.flamesclient.ru/web/apns-prod.pem:Rh3xwaex9g -H \"apns-topic: org.reactjs.native.example.GuruOnline\" --http2  https://api.push.apple.com/3/device/${device}";
                shell_exec($curl_query);
                // $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device)) . chr(0) . chr(strlen($payload)) . $payload;
                // // $apns_message = $payload;
                // // var_dump($payload);die();
                // // var_dump($apns_message);die();
                // if (fwrite($apns, $apns_message)) {
                //     $success++;
                //     // var_dump($apns_message);die();
                //     // echo("sent\n");
                // } else {
                //     echo("failed \n");
                // }
            }
            // echo("fetch done\n");
            // socket_close($apns);
            // fclose($apns);
            // return $success;
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
