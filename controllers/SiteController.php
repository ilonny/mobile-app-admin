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
use app\models\Push;
use yii\web\UploadedFile;



class SiteController extends Controller
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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // phpinfo();die();
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays about item list by type.
     *
     * @return string
     */
    public function actionList($type){
        if ($type == 'author') {
            $type_id = 1;
        } else {
            $type_id = 2;
        }

        //запрос логина (да, не сделал через AccessControl)
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
        //если были данные с формы на добавление, добавим элемент
        $model = new Item;
        if ($model->load(Yii::$app->request->post())){
            $model->save();
            $this->redirect("/site/list?type={$type}");
        }
        //найдем всех для вывода
        $items = Item::find()->where(['item_type_id' => $type_id])->all();
        return $this->render('list', [
            'type' => $type,
            'items' => $items,
        ]);
    }

    public function actionEdit($id){
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
        $model = Item::findOne($id);
        $answer = '';
        if (Yii::$app->request->post()){
            $model->load(Yii::$app->request->post());
            if ($model){
                if ($model->update()){
                    $answer = 'Успешно отредактировано';
                }
            }
        }

        return $this->render('edit', [
            'model' => $model,
            'answer' => $answer,
        ]);
    }

    public function actionDelete($id){
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
        $model = Item::findOne($id);
        $answer = '';
        if ($model->delete()){
            $answer = 'Успешно удалено';
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        } else {
            $answer = 'Произошла ошибка';
        }
    }


    public function actionQuotes(){
        if ($_GET['item_id']) {
            $quotes = Quote::find()->andWhere(['item_id' => $_GET['item_id']])->orderBy('id DESC')->all();
        } else {
            $quotes = Quote::find()->orderBy('id DESC')->all();
        }
        $items = Item::find()->all();
        Yii::$app->params['uploadPath'] = Yii::$app->basePath . '/web/uploads/';
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
        //если были данные с формы на добавление, добавим элемент
        $model = new Quote;
        if ($model->load(Yii::$app->request->post())){
            $image = UploadedFile::getInstance($model, 'img_src');
            $model->img_src = $image->name;
            $ext = end((explode(".", $image->name)));
            $model->img_src = Yii::$app->security->generateRandomString().".{$ext}";
            $path = Yii::$app->params['uploadPath'] . $model->img_src;
            // var_dump($model->item_id);
            // die();
            if ($model->save()){
                if ($image){
                    $image->saveAs($path);
                }
                // Token::sendPushForGroupAndroid($model->item_id, $model->text_short, $model->id, $model->title);
                // Token::sendPushForGroup($model->item_id, $model->text_short, $model->id, $model->title);
                //success saved file
                $this->redirect('/site/quotes');
            }
        }
        return $this->render('quotes', [
            'quotes' => $quotes,
            'items' => $items,
        ]);
    }

    public function actionPush(){
        $city_name = null;
        if (Yii::$app->user->identity->username != 'admin') {
            $city_name = Yii::$app->user->identity->username;
        } else {
            if ($_POST['Push']['other'] != 'admin') {
                $city_name = $_POST['Push']['other'];
            }
        }
        set_time_limit(1200);
        ini_set("max_execution_time", "1200");
        if ($city_name) {
            $pushes = Push::find()->andWhere(['other' => $city_name])->all();
            $tokens = Token::find()->andWhere(['city' => $city_name])->andWhere(['city_push' => 1])->all();
        } else {
            $pushes = Push::find()->all();
            $tokens = Token::find()->all();
        }

        // var_dump($tokens);die();
        $model = new Push;
        if ($model->load(Yii::$app->request->post())){
            if ($model->save()){
                // Token::sendPushForGroupAndroidWithAction($model->payload);
                // Token::sendPushForGroupWithAction($model->payload);
                foreach ($tokens as $key => $token) {
                    // if ($token->id == 1707) {
                    // if ($token->id == 1625) {
                        // var_dump($token->id);
                        // die();
                        if (json_decode($token->token)->os == 'ios'){
                            $token_platform = 'ios';
                        } else {
                            $token_platform = 'android';
                        }
                        if ($token->lang == 'eng' || $token->lang == 'en') {
                            $lang = 'en';
                        } else if ($token->lang == 'es') {
                            $lang = 'es';
                        } else {
                            $lang = 'ru';
                        }
                        $payload_title = '';
                        if ($lang == 'es') {
                            $payload_body = $model->payload_es;
                        } else if ($lang == 'en') {
                            $payload_body = $model->payload_eng;
                        } else {
                            $payload_body = $model->payload;
                        }
                        // $payload_body = $lang == 'ru' ? $model->payload : $lang == 'en' ? $model->payload_eng : $model->payload_es;
                        // $payload_title = strip_tags($payload_title);
                        // $payload_body = strip_tags($payload_body);
                        // var_dump($payload_body);die();
                        if ($payload_body) {
                            if ($token_platform == 'ios'){
                                $payload = json_encode([
                                    "need_alert" => true,
                                    "aps" => [
                                        // "alert" => [
                                        //     "title" => $payload_title,
                                        //     // "subtitle" => $quote->title,
                                        //     "body" => $payload_body
                                        // ],
                                        "alert" => $payload_body,
                                        "sound" => "default",
                                    ],
                                ], JSON_UNESCAPED_UNICODE);
                                $device = $token->other;
                                $curl_query = "curl -d '${payload}' --cert /var/www/www-root/data/www/app.harekrishna.ru/web/GuruOnlineApns.pem:Rh3xwaex9g -H \"apns-topic: org.reactjs.native.example.GuruOnline\" --http2  https://api.push.apple.com/3/device/${device}";
                                $curl_result = shell_exec($curl_query);
                                // var_dump($curl_query);
                                // var_dump($curl_result);
                                // file_put_contents('alert_push.txt', 'start ', FILE_APPEND);
                                // file_put_contents('alert_push.txt', print_r($curl_query, true), FILE_APPEND);
                                // file_put_contents('alert_push.txt', print_r($curl_result, true), FILE_APPEND);
                                // file_put_contents('alert_push.txt', ' end ', FILE_APPEND);
                                // die();
                            } else {
                                $android_push_body = json_encode([
                                    'to' => $token->other,
                                    "priority" => "high",
                                    'data' => array(
                                        'body' => array(
                                            'text' => $payload_body,
                                            'need_alert' => true,
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
                                // file_put_contents('alert_push.txt', 'start ', FILE_APPEND);
                                // file_put_contents('alert_push.txt', print_r($response, true), FILE_APPEND);
                                // file_put_contents('alert_push.txt', ' end ', FILE_APPEND);
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
                        // }
                    }
                }
                $this->redirect('/site/push');
            }
        }
        return $this->render('push', [
            'pushes' => $pushes,
        ]);
    }

    public function actionEditQuote($id){
        Yii::$app->params['uploadPath'] = Yii::$app->basePath . '/web/uploads/';
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
        $model = Quote::findOne($id);
        $answer = '';
        if (Yii::$app->request->post()){
            $prev_img = $model->img_src;
            $model->load(Yii::$app->request->post());
            // var_dump($prev_img);
            if ($image = UploadedFile::getInstance($model, 'img_src')){
                $image = UploadedFile::getInstance($model, 'img_src');
                $model->img_src = $image->name;
                $ext = end((explode(".", $image->name)));
                $model->img_src = Yii::$app->security->generateRandomString().".{$ext}";
                $path = Yii::$app->params['uploadPath'] . $model->img_src;
            } else {
                $model->img_src = $prev_img;
            }
            if ($model->update()){
                if ($image){
                    $image->saveAs($path);
                }
                $answer = 'Успешно отредактировано';
            }
        }
        // var_dump($model->img_src);
        // die();
        return $this->render('edit-quote', [
            'model' => $model,
            'answer' => $answer,
        ]);
    }

    public function actionDeleteQuote($id){
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
        $model = Quote::findOne($id);
        $answer = '';
        if ($model->delete()){
            $answer = 'Успешно удалено';
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        } else {
            $answer = 'Произошла ошибка';
        }
    }
}
