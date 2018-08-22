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
use app\models\ReaderBook;
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
        // return $items;
        // Yii::$app->response->format = Response::FORMAT_JSON;
        if ($items == '[all]'){
            $models = Quote::find()->orderBy('id DESC')->all();
        } else {
            $req = JSON::decode($items);
            $models = Quote::find()->where(['in', 'item_id', $req])->orderBy('id DESC')->all();
        }
        foreach ($models as $key => $model){
            $res[$key]['id'] = $model->id;
            $res[$key]['title'] = $model->title;
            $res[$key]['text_short'] = $model->text_short;
            $res[$key]['author_name'] = $model->getAuthorName();
        }
        // return JSON::encode($res, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        return JSON::encode($res);
    }

    public function actionFavorites($items){
        // return $items;
        // Yii::$app->response->format = Response::FORMAT_JSON;
        $req = JSON::decode($items);
        $models = Quote::find()->where(['in', 'id', $req])->all();
        foreach ($models as $key => $model){
            $res[$key]['id'] = $model->id;
            $res[$key]['title'] = $model->title;
            $res[$key]['text_short'] = $model->text_short;
            $res[$key]['author_name'] = $model->getAuthorName();
        }
        // return JSON::encode($res, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        return JSON::encode($res);
    }

    public function actionQuote($id){
        $id = intval($id);
        $model = Quote::findOne($id);
        $this->layout = 'api';
        if (!$id || !$model){
            return 'wrong parameters';
        }
        return $this->render('quote', [
            'model' => $model
        ]);
    }

    public function actionSetToken($token, $settings){
        //safe or update
        $token_clear = json_decode($token);
        $token_clear = $token_clear->token;
        if (!$token_clear){
            $token = str_replace('"', '', $token);
            $token_arr['token'] = $token;
            $token_arr['os'] = 'android';
            $token = $token_arr;
            $token_clear = $token['token'];
            $token = json_encode($token);
        }
        $model = Token::find()->where(['token' => $token])->one();
        if (!$model){
            $model = new Token();
            $model->token = $token;
            $model->settings = $settings;
            $model->other = $token_clear;
            if ($model->save()){
                return 'success';
            } else {
                return 'error';
            }
        } else {
            $model->settings = $settings;
            if ($model->update()){
                return 'success';
            } else {
                return 'error12';
            }
        }
    }

    public function actionGetReaderBooks(){
        $books = ReaderBook::find()->all();
        $books_arr = [];
        foreach ($books as $book){
            array_push($books_arr, [
                'id' => $book->id,
                'name' => $book->name,
                'description' => $book->description,
                'author' => $book->readerAuthor->name,
            ]);
        }
        return json_encode($books_arr, JSON_UNESCAPED_UNICODE);
    }

    public function actionGetReaderBook($id){
        $book = ReaderBook::findOne($id);
        if (!is_file("$book->file_src")) {
            throw new \yii\web\NotFoundHttpException('The file does not exists.');
        }
        return Yii::$app->response->sendFile("$book->file_src", 'book.epub');
        // return var_dump($book);

    }
}