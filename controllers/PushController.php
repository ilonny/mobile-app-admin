<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Item;
use app\models\Quote;
use app\models\UploadForm;
use app\models\Token;
use yii\web\UploadedFile;
use yii\helpers\Json;


class PushController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionTestPushApple(){
        function replace_unicode_escape_sequence($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }
        function send_ios_push($message) {
            $sound = 'default';
            $development = false;
            $payload = array();
            $payload["aps"] = array('alert' => $message, 'sound' => $sound);
            $payload = json_encode($payload);
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
            if (file_exists($apns_cert)) 
            echo("cert file exists\n"); 
            else 
            echo("cert file not exists\n");; 
            $success = 0;
            $stream_context = stream_context_create();
            stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
            stream_context_set_option($stream_context, 'ssl', 'passphrase', 'Rh3xwaex9g');
        
            $apns = stream_socket_client('ssl://' . $apns_url . ':' . $apns_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);
            
            $device_tokens =array(
                "6239f75466558adec9e518f5daacf97410b57968b74dfce43e430308939bf255",
            );
        
            foreach($device_tokens as $device) {
                $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device)) . chr(0) . chr(strlen($payload)) . $payload;
                if (fwrite($apns, $apns_message)) {
                    $success++;
                    echo("sent\n");
                } else {
                    echo("failed \n");
                }
            }
            echo("fetch done\n"); 
            socket_close($apns);
            fclose($apns);
            return $success;
        }
        
        send_ios_push('ты пидор');
    }
    public function actionDailyPush(){
        $settings = Item::find()->all();
        foreach($settings as $setting){
            //для каждого автора найдем список цитат
            $quotes = Quote::find()->andWhere(['item_id' => $setting->id])->all();
            //возьмем из них рандомную
            $rand_id = rand(0, count($quotes)-1);
            $quote = $quotes[$rand_id];
            if ($quote->text_short){
                Token::sendPushForGroupAndroid($setting->id, $quote->text_short, $quote->id, $quote->title);
                Token::sendPushForGroup($setting->id, $quote->text_short, $quote->id);
            }
        }
    }
}