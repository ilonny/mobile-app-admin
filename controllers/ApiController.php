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
use yii\web\UploadedFile;
use yii\helpers\Json;


class ApiController extends Controller
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

    public function actionItems(){
        $models = Item::find()->all();
        $authors = Item::find()->where(['item_type_id' => 1])->all();
        $books = Item::find()->where(['item_type_id' => 2])->all();
        $return['all_items'] = $models;
        $return['authors'] = $authors;
        $return['books'] = $books;
        return Json::encode($return);
    }

    public function actionQuotes($items){
        $req = JSON::decode($items);
        $models = Quote::find()->where(['in', 'item_id', $req])->all();
        foreach ($models as $key => $model){
            $res[$key]['id'] = $model->id;
            $res[$key]['title'] = $model->title;
            $res[$key]['text_short'] = $model->text_short;
            $res[$key]['author_name'] = $model->getAuthorName();
        }
        return JSON::encode($res);
    }
}