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
        $quotes = Quote::find()->all();
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
            Token::sendPushForGroupAndroid($setting->id, $quote->text_short);
            Token::sendPushForGroup($model->item_id, $model->text_short);
            if ($model->save()){
                if ($image){
                    $image->saveAs($path);
                }
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
        $pushes = Push::find()->all();
        $model = new Push;
        if ($model->load(Yii::$app->request->post())){
            if ($model->save()){
                Token::sendPushForAllAndroid($model->payload);
                Token::sendPushForAll($model->payload);
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
