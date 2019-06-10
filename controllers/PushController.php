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
use app\models\BitrixPushLogs;

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
        
    }
    public function actionDailyPush($offset = 0){
        set_time_limit(1200);
        ini_set("max_execution_time", "1200");
        file_put_contents('log.txt', $_SERVER['REMOTE_ADDR'].' '.time().'/n', FILE_APPEND);
        if ($_SERVER['REMOTE_ADDR'] == '194.58.111.232' || $_GET['qq'] == '655535'){
            //сформируем массив из рандомных цитат которые мы будем отправлять
            $settings = Item::find()->all();
            $random_quotes = [];
            $random_quotes_eng = [];
            $random_quotes_es = [];
            foreach($settings as $setting){
                //для каждого автора найдем список цитат
                $quotes = Quote::find()
                    ->andWhere(['item_id' => $setting->id])
                    ->andWhere(['is not', 'title', NULL])
                    ->andWhere(['<>', 'title', ''])
                    ->all();
                $quotes_eng = Quote::find()
                    ->andWhere(['item_id' => $setting->id])
                    ->andWhere(['is not', 'title_eng', NULL])
                    ->andWhere(['<>', 'title_eng', ''])
                    ->all();
                $quotes_es = Quote::find()
                    ->andWhere(['item_id' => $setting->id])
                    ->andWhere(['is not', 'title_es', NULL])
                    ->andWhere(['<>', 'title_es', ''])
                    ->all();
                //возьмем из них рандомную
                $rand_id = rand(0, count($quotes)-1);
                $rand_id_eng = rand(0, count($quotes_eng)-1);
                $rand_id_es = rand(0, count($quotes_es)-1);
                if ($quotes[$rand_id]->text_short){
                    $random_quotes[] = $quotes[$rand_id];
                }
                if ($quotes_eng[$rand_id_eng]->text_short_eng){
                    $random_quotes_eng[] = $quotes_eng[$rand_id_eng];
                }
                if ($quotes_es[$rand_id_es]->text_short_es){
                    $random_quotes_es[] = $quotes_es[$rand_id_es];
                }
            }
            //берем теперь все токены и прогоням их. отправляя пуш цитаты, если токен подписан на источник этой цитаты
            if ($offset == 0) {
                $tokens = Token::find()->limit(10)->all();
                // $tokens = Token::find()->where(['>=', 'id', 269])->limit(10)->all();
            } else {
                $tokens = Token::find()->offset($offset)->limit(10)->all();
            }
            // $tokens = Token::find()->andWhere(['id' => 1178])->limit(1)->all();
            // $tokens = Token::find()->andWhere(['id' => 1172])->limit(1)->all();
            // $tokens = Token::find()->andWhere(['id' => 1079])->limit(1)->all();
            foreach ($tokens as $token){
                //удалим кривые токены
                    //пока не воркает нормально
                    if (!$token->other){
                        $token->delete();
                        continue;
                    }
                //
                //определимся ios или андроид
                if (json_decode($token->token)->os == 'ios'){
                    $token_platform = 'ios';
                } else {
                    $token_platform = 'android';
                }
                //для каждой цитаты определимся есть ли подписка? если есть - отправим пуш
                if ($token->lang == 'eng' || $token->lang == 'en') {
                    $quotes_arr = $random_quotes_eng;
                    $lang = 'eng';
                } else if ($token->lang == 'es') {
                    $quotes_arr = $random_quotes_es;
                    $lang = 'es';
                } else {
                    $quotes_arr = $random_quotes;
                    $lang = 'ru';
                }
                foreach ($quotes_arr as $key => $quote){
                    $need_to_push = false;
                    if ($token->settings == 'all'){
                        $need_to_push = true;
                    } else {
                        $settings = json_decode($token->settings, true);
                        if (in_array($quote->item_id, $settings)){
                            $need_to_push = true;
                        }
                    }
                    //если есть подписка на такой вид цитат, отправим пуш.
                    switch ($lang) {
                        case 'eng':
                            $payload_title = $quote->getAuthorNameEng();
                            $payload_body = $quote->text_short_eng;
                            break;
                        case 'ru':
                            $payload_title = $quote->getAuthorName();
                            $payload_body = $quote->text_short;
                            break;
                        case 'es':
                            $payload_title = $quote->getAuthorNameEs();
                            $payload_body = $quote->text_short_es;
                            break;
                        default:
                            $payload_title = $quote->getAuthorName();
                            $payload_body = $quote->text_short;
                            break;
                    }
                    if ($need_to_push){
                        //тут пойдет разделение на платформы
                        if ($token_platform == 'ios'){
                            $payload = json_encode([
                                "quote_id" => $quote->id,
                                "aps" => [
                                    "alert" => [
                                        "title" => $payload_title,
                                        // "subtitle" => $quote->title,
                                        "body" => $payload_body
                                    ],
                                    "sound" => "default",
                                ],
                            ], JSON_UNESCAPED_UNICODE);
                            $device = $token->other;
                            $curl_query = "curl -d '${payload}' --cert /var/www/www-root/data/www/app.harekrishna.ru/web/apns-prod.pem:Rh3xwaex9g -H \"apns-topic: org.reactjs.native.example.GuruOnline\" --http2  https://api.push.apple.com/3/device/${device}";
                            $curl_result = shell_exec($curl_query);
                            // var_dump($curl_query);
                            // var_dump($curl_result);
                            // die();
                            if ($curl_result != NULL){
                                $result_arr = json_decode($curl_result, true);
                                // if ($result_arr['reason']){
                                //     $token->delete();
                                //     continue 2;
                                //     //берем следующий токен 
                                // }
                            }
                        } else {
                            $android_push_body = json_encode([
                                'to' => $token->other,
                                "priority" => "high",
                                'data' => array(
                                    'body' => array(
                                        'text' => $payload_body,
                                        'q_id' => intval($quote->id),
                                    ),
                                    'title' => $payload_title,
                                )
                            ], JSON_UNESCAPED_UNICODE);
                            $ch = curl_init('https://fcm.googleapis.com/fcm/send');
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $android_push_body);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Authorization: key=AAAAmLg0GRc:APA91bGaOgw6-8zB6Q_o7A-Qf5BU7ofEQqM5UoMAgIySYgcFQ3aS1z9V9W-Wk9Xa9qRrqaQ47qfo7tzAi4uY-4IzgAPpesbwVOYZQ4QX94VFCQvGLpSS4qaOwJpritlwf-n7BWsvH5jO9sKZAyA56vdcL1Gt1mlKtg'
                            ));
                            $response = curl_exec($ch);
                            $response = json_decode($response, true);
                            try {
                                if ($response['failure']){
                                    // if ($token->error == null) {
                                    //     $token->error  = '1';
                                    // } else {
                                    //     $token->error = strval($token->error+1);
                                    // }
                                    $token->error = json_encode($response);
                                    $token->update();
                                    // continue 2;
                                } else {
                                    $token->error = '';
                                    $token->update();
                                }
                            } catch (Exception $e) {}
                        }
                    }
                }
            }
            ////
            //Старый дурацкий код
            // $settings = Item::find()->all();
            // foreach($settings as $setting){
            //     //для каждого автора найдем список цитат
            //     $quotes = Quote::find()->andWhere(['item_id' => $setting->id])->all();
            //     //возьмем из них рандомную
            //     $rand_id = rand(0, count($quotes)-1);
            //     $quote = $quotes[$rand_id];
            //     if ($quote->text_short){
            //         Token::sendPushForGroupAndroid($setting->id, $quote->text_short, $quote->id, $quote->title);
            //         Token::sendPushForGroup($setting->id, $quote->text_short, $quote->id);
            //     }
            // }
            // конец старый дурацкий код
        }
    }

    public function actionBitrixPush(){
        //
        set_time_limit(1200);
        ini_set("max_execution_time", "1200");  
        $types = ['content', 'read', 'look', 'listen', 'important', 'news'];
        
        foreach ($types as $key => $type) :
            $api_arr = shell_exec("curl -X GET https://harekrishna.ru/mobile-api/get-list.php?type=${type}");
            $last_item = json_decode($api_arr, true);
            $last_item = $last_item[0];
            $existed_model = BitrixPushLogs::find()->where(['site_id' => $last_item['ID']])->all();
            if (!$existed_model):
                $model = new BitrixPushLogs();
                $model->type = $type;
                $model->site_id = $last_item['ID'];
                $model->news_title = $last_item['NAME'];
                $model->save();
                // var_dump("curl -X GET https://harekrishna.ru/mobile-api/get-list.php?type=${type}");die();
                // var_dump('existing');die();
                //
                // $s1 = urldecode($_GET['data']);
                // $data = json_decode($s1, true);
                $data = $last_item;
                $data['PREVIEW_TEXT'] = str_replace("&nbsp;", " ", $data['PREVIEW_TEXT']);
                file_put_contents('bitrix_push.txt', print_r($data, true), FILE_APPEND);
                // var_dump($data['PREVIEW_TEXT']);die();
                // var_dump($_GET);
                // echo 123;
                $tokens = Token::find()->where(['version' => '2'])->andWhere(['<>', 'lang', 'eng'])->andWhere(['<>', 'lang', 'en'])->andWhere(['<>', 'lang', 'es'])->all();
                // var_dump($tokens);die();
                // $tokens = Token::find()->where(['id' => 1233])->all();
                // $tokens = Token::find()->where(['id' => 1079])->all();
                // sleep(600);
                // for ($i = 1; $i<=100; $i++) {
                //     $tokens[] = $tokens[0];
                // }
                
                foreach ($tokens as $key => $token) {
                    
                    $need_push = true;
                    $site_settings_arr = json_decode($token->news_settings, true);
                    
                    switch ($type) {
                        case 'look':
                            if (!in_array('look', $site_settings_arr)) $need_push = false;
                            $title = "Новое в разделе  \"Смотреть\"";
                            break;
                        case 'content':
                            if (!in_array('content', $site_settings_arr)) $need_push = false;
                            $title = "Новое в разделе  \"Новости\"";
                            break;
                        case 'news':
                            if (!in_array('news', $site_settings_arr)) $need_push = false;
                            $title = "Новое в разделе  \"Новости\"";
                            break;
                        case 'listen':
                            if (!in_array('listen', $site_settings_arr)) $need_push = false;    
                            $title = "Новое в разделе  \"Слушать\"";
                            break;
                        case 'read':
                            if (!in_array('read', $site_settings_arr)) $need_push = false;    
                            $title = "Новое в разделе  \"Читать\"";
                            break;
                        case 'important':
                            if (!in_array('important', $site_settings_arr)) $need_push = false;    
                            $title = "Новое в разделе  \"Это важно\"";
                            break;
                        default:
                            # code...
                            $need_push = false;
                            break;
                    }

                    if (!$data['ID'] || !$data['NAME'] || !$model->id){
                        $need_push = false;
                    }
                    // var_dump($data);die();
                    if ($need_push){
                        if (json_decode($token->token)->os == 'ios'){
                            $token_platform = 'ios';
                        } else {
                            $token_platform = 'android';
                        }
                        if ($token_platform == 'ios'){
                            $payload = json_encode([
                                "news_id" => $data['ID'],
                                "news_title" => $data['NAME'],
                                "aps" => [
                                    "alert" => [
                                        "title" => $title,
                                        // "subtitle" => $quote->title,
                                        "body" => $data['PREVIEW_TEXT'],
                                    ],
                                    "sound" => "default",
                                ],
                            ], JSON_UNESCAPED_UNICODE);
                            // echo $payload; die();
                            $device = $token->other;
                            $curl_query = "curl -d '${payload}' --cert /var/www/www-root/data/www/app.harekrishna.ru/web/apns-prod.pem:Rh3xwaex9g -H \"apns-topic: org.reactjs.native.example.GuruOnline\" --http2  https://api.push.apple.com/3/device/${device}";
                            // $curl_query = "curl -d '${payload}' --cert /var/www/www-root/data/www/app.harekrishna.ru/web/apns-dev.pem:Rh3xwaex9g -H \"apns-topic: org.reactjs.native.example.GuruOnline\" --http2  https://api.push.apple.com/3/device/${device}";
                            $curl_result = shell_exec($curl_query);
                            // var_dump($curl_query);
                            // var_dump($curl_result);
                            // die();
                        } else {
                            $android_push_body = json_encode([
                                'to' => $token->other,
                                "priority" => "high",
                                'data' => array(
                                    'body' => array(
                                        'text' => $data['PREVIEW_TEXT'],
                                        'q_id' => 'false',
                                        "news_id" => intval($data['ID']),
                                        "news_title" => $data['NAME'],
                                    ),
                                    'title' => $title
                                )
                            ], JSON_UNESCAPED_UNICODE);
                            // var_dump($android_push_body);die();
                            $ch = curl_init('https://fcm.googleapis.com/fcm/send');
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $android_push_body);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Authorization: key=AAAAmLg0GRc:APA91bGaOgw6-8zB6Q_o7A-Qf5BU7ofEQqM5UoMAgIySYgcFQ3aS1z9V9W-Wk9Xa9qRrqaQ47qfo7tzAi4uY-4IzgAPpesbwVOYZQ4QX94VFCQvGLpSS4qaOwJpritlwf-n7BWsvH5jO9sKZAyA56vdcL1Gt1mlKtg'
                            ));
                            $response = curl_exec($ch);
                            $response = json_decode($response, true);
                            // var_dump($response);die();
                        }
                    }
                }
                file_put_contents('bitrix_push.txt', 'start ', FILE_APPEND);
                file_put_contents('bitrix_push.txt', print_r($data, true), FILE_APPEND);
                file_put_contents('bitrix_push.txt', print_r($curl_query, true), FILE_APPEND);
                file_put_contents('bitrix_push.txt', print_r($curl_result, true), FILE_APPEND);
                file_put_contents('bitrix_push.txt', ' end ', FILE_APPEND);
            endif;
        endforeach;
    }
        // var_dump($token_platform);die();
        
    public function actionDailyEcadash() {
        $type = $_GET['type']; //today or tomorrow
        $today_date = 
        $cities_shedule = [];
        set_time_limit(1200);
        ini_set("max_execution_time", "1200");
        $tokens = Token::find()->andWhere(['version' => 3])->all();
        // $tokens = Token::find()->andWhere(['id' => 1376])->all();
        foreach ($tokens as $key => $token) {
            if (json_decode($token->token)->os == 'ios'){
                $token_platform = 'ios';
            } else {
                $token_platform = 'android';
            }
            if ($token->city) {
                $token_city = $token->city;
            } else {
                $token_city = 'moscow';
            }
            if ($token->lang == 'eng' || $token->lang == 'en') {
                $lang = 'en';
            } else {
                $lang = 'ru';
            }

            if (!$cities_shedule[$token_city][$lang]) {
                if($curl = curl_init()) {
                    curl_setopt($curl, CURLOPT_URL, 'http://vaishnavacalendar.org/json/'.$token_city.'/534/'.$lang);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
                    $out = curl_exec($curl);
                    $cities_shedule[$token_city][$lang] = json_decode($out, JSON_UNESCAPED_UNICODE);
                    curl_close($curl);
                }
            }
            $shedule = $cities_shedule[$token_city][$lang];
            // echo '<pre>';
            // var_dump($cities_shedule);
            // die();
            // echo '</pre>';
            $today = date('Y-m-d');
            $tomorrow = new \DateTime();
            $tomorrow->modify('+1 day');
            $tomorrow = $tomorrow->format('Y-m-d');
            if ($_GET['date']) {
                if ($type == 'today') {
                    $today = $_GET['date'];
                } else {
                    $tomorrow = new \DateTime($_GET['date']);
                    $tomorrow->modify('+1 day');
                    $tomorrow = $tomorrow->format('Y-m-d');
                }
            }
            // $today = '2019-06-07';
            // $tomorrow = '2019-06-07';

            $shedule_item = false;
            foreach ($shedule as $key_s => $shedule_item_s) {
                if ($type == 'today') {
                    if ($shedule_item_s['date'] == $today) {
                        $shedule_item = $shedule_item_s;
                    }
                } else {
                    if ($shedule_item_s['date'] == $tomorrow) {
                        $shedule_item = $shedule_item_s;
                    }
                }
            }
            // var_dump($tomorrow);die();
            // var_dump($shedule);die();
            if ($shedule_item) {
                $payload_title = ($type == 'today' ? ($lang == 'ru' ? 'Сегодня: ' : 'Today: ').date('d.m.Y', strtotime($shedule_item['date'])) : ($lang == 'ru' ? 'Завтра ' : 'Tomorrow ').'('.date('d.m.Y', strtotime($tomorrow)).')');
                $payload_body = (($shedule_item['festivals_str'] || $shedule_item['holy_days_str']) ? $shedule_item['festivals_str'].' '.$shedule_item['holy_days_str'] : ($shedule_item['shv_str'] ? $shedule_item['shv_str'] : ''));
                $payload_title = strip_tags($payload_title);
                $payload_body = strip_tags($payload_body);
                if ($token_platform == 'ios'){
                    $payload = json_encode([
                        "aps" => [
                            "alert" => [
                                "title" => $payload_title,
                                // "subtitle" => $quote->title,
                                "body" => $payload_body
                            ],
                            "sound" => "default",
                        ],
                    ], JSON_UNESCAPED_UNICODE);
                    $device = $token->other;
                    $curl_query = "curl -d '${payload}' --cert /var/www/www-root/data/www/app.harekrishna.ru/web/apns-prod.pem:Rh3xwaex9g -H \"apns-topic: org.reactjs.native.example.GuruOnline\" --http2  https://api.push.apple.com/3/device/${device}";
                    $curl_result = shell_exec($curl_query);
                    // var_dump($curl_query);
                    // var_dump($curl_result);
                    // die();
                } else {
                    $android_push_body = json_encode([
                        'to' => $token->other,
                        "priority" => "high",
                        'data' => array(
                            'body' => array(
                                'text' => $payload_body,
                            ),
                            'title' => $payload_title,
                        )
                    ], JSON_UNESCAPED_UNICODE);
                    $ch = curl_init('https://fcm.googleapis.com/fcm/send');
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $android_push_body);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Authorization: key=AAAAmLg0GRc:APA91bGaOgw6-8zB6Q_o7A-Qf5BU7ofEQqM5UoMAgIySYgcFQ3aS1z9V9W-Wk9Xa9qRrqaQ47qfo7tzAi4uY-4IzgAPpesbwVOYZQ4QX94VFCQvGLpSS4qaOwJpritlwf-n7BWsvH5jO9sKZAyA56vdcL1Gt1mlKtg'
                    ));
                    $response = curl_exec($ch);
                    $response = json_decode($response, true);
                    try {
                        if ($response['failure']){
                            // if ($token->error == null) {
                            //     $token->error  = '1';
                            // } else {
                            //     $token->error = strval($token->error+1);
                            // }
                            $token->error = json_encode($response);
                            $token->update();
                            // continue 2;
                        } else {
                            $token->error = '';
                            $token->update();
                        }
                    } catch (Exception $e) {}
                }
            }
        }
        // var_dump($cities_shedule);die();
    }
        // $data = $_GET['data'];
}