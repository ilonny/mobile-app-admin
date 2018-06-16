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
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Displays about item list by type.
     *
     * @return string
     */
    public function actionList($type){
        //запрос логина (да, не сделал через AccessControl)
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
        //если были данные с формы на добавление, добавим элемент
        $model = new Item;
        $model->load(Yii::$app->request->post());
        if ($model){
            $model->save();
        }
        //найдем всех для вывода
        if ($type == 'author') {
            $type_id = 1;
        } else {
            $type_id = 2;
        }
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
}
