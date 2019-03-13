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
use app\models\AudioAuthor;
use app\models\AudioBook;
use app\models\Audiofile;
use app\models\Toc;
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
            $res[$key]['text'] = $model->text;
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

    public function actionSetToken($token, $settings, $news_settings = '["content","read","look","listen","important"]', $version = '1'){
        if ($news_settings == 'news,read,look,listen,important' || $news_settings == "['news', 'read', 'look', 'listen', 'important']") {
            $news_settings = '["content","read","look","listen","important"]';
        }
        if (!$news_settings){
            $news_settings = '["content","read","look","listen","important"]';
        }
        // if ($news_settings == 'old'){
        //     $news_settings = '["content","read","look","listen","important"]';
        // }
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
            $model->settings = $settings == 'old' ? $model->settings : $settings;
            $model->news_settings = $news_settings;
            $model->version = $version;
            $model->other = $token_clear;
            if ($model->save()){
                return 'success';
            } else {
                return 'error';
            }
        } else {
            $model->settings = $settings == 'old' ? $model->settings : $settings;
            if ($model->news_settings && $news_settings == 'old'){
                $model->news_settings = $model->news_settings;
                // var_dump($model->news_settings);
                // die();
            } else if (!$model->news_settings && $news_settings == 'old'){
                $model->news_settings = '["content","read","look","listen","important"]';
            } else {
                $model->news_settings = $news_settings;
            }
            // $model->news_settings = $news_settings;
            $model->version = $version;
            // var_dump($model);die();
            if ($model->update()){
                return 'success';
            } else {
                return 'error12';
            }
        }
    }

    public function actionGetReaderBooks(){
        // $count_books = count(ReaderBook::find()->all());
        // $page_count = ceil($count_books / 5);
        // $response['page_count'] = $page_count;
        $books = ReaderBook::find()->all();
        $response = [];
        $books_arr = [];
        foreach ($books as $book){
            $book_path = explode('.', $book->file_src);
            $files=\yii\helpers\FileHelper::findFiles($book_path[0]);
            $cover_src = "";
            foreach ($files as $file){
                $file_ext = explode('.', $file);
                if ($file_ext[1] == 'jpg'){
                    $cover_src = $file;
                    // $imagedata = file_get_contents($file);
                    // // alternatively specify an URL, if PHP settings allow
                    // $base64 = base64_encode($imagedata);
                    break;
                }
            }
            array_push($books_arr, [
                'id' => $book->id,
                'name' => $book->name,
                'description' => $book->description,
                'author' => $book->readerAuthor->name,
                'file_src' => $book->file_src,
                'cover_src' => $cover_src,
                // 'base64' => $base64,
            ]);
        }
        $response['books'] = $books_arr;
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function actionGetReaderBook($id){
        $book = ReaderBook::findOne($id);
        if (!is_file("$book->file_src")) {
            throw new \yii\web\NotFoundHttpException('The file does not exists.');
        }
        // file_put_contents('test.txt', 'accessed//', FILE_APPEND);
        return Yii::$app->response->sendFile("$book->file_src", 'book.epub');
        // return var_dump($book);

    }

    public function actionGetTocs($book_id){
        $models = Toc::find()->andWhere(['book_id' => $book_id])->all();
        $arr = [];
        foreach ($models as $model){
            $audio_book_name = "";
            if ($model->audio_book_id){
                $audioBook = AudioBook::findOne($model->audio_book_id);
                $audio_book_name = $audioBook->name;
            }
            array_push($arr, [
                'id' => $model->id,
                'app_href' => $model->app_href,
                'title' => $model->title,
                'book_id' => $model->book_id,
                'audio_book_id' => $model->audio_book_id,
                'audio_book_name' => $audio_book_name,
                'audiofile_id' => $model->audiofile_id,
            ]);
        }
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    public function actionDebugData($debug_data){
        file_put_contents('debug.txt', $debug_data, FILE_APPEND);
    }

    public function actionGetAudioBooks(){
        $books = AudioBook::find()->all();
        $books_arr = [];
        foreach ($books as $book){
            $audiofiles = Audiofile::find()->andWhere(['audio_book_id' => $book->id])->orderBy('sort')->all();
            $audiofiles_arr = [];
            foreach ($audiofiles as $audiofile){
                $toc_href = "";
                $reader_book_name = "";
                $reader_book_src = "";
                if ($audiofile->toc_id){
                    $toc = Toc::findOne($audiofile->toc_id);
                    $toc_href = $toc->app_href;
                }
                if ($audiofile->reader_book_id){
                    $reader_book = ReaderBook::findOne($audiofile->reader_book_id);
                    $reader_book_name = $reader_book->name;
                    $reader_book_src = $reader_book->file_src;
                }
                array_push($audiofiles_arr, [
                    'id' => $audiofile->id,
                    'name' => $audiofile->name,
                    'description' => $audiofile->description,
                    'file_src' => $audiofile->file_src,
                    'reader_book_id' => $audiofile->reader_book_id,
                    'toc_id' => $audiofile->toc_id,
                    'toc_href' => $toc_href,
                    'reader_book_name' => $reader_book_name,
                    'reader_book_src' => $reader_book_src,
                ]);
            }
            array_push($books_arr, [
                'id' => $book->id,
                'name' => $book->name,
                'description' => $book->description,
                'author' => $book->audioAuthor->name,
                'audiofiles' => $audiofiles_arr
            ]);
        }
        return json_encode($books_arr, JSON_UNESCAPED_UNICODE);
    }

    public function actionGetAudioFiles($book_id){
        $books = Audiofile::find()->andWhere(['audio_book_id' => $book_id])->orderBy('sort')->all();
        $books_arr = [];
        foreach ($books as $book){
            $toc_href = "";
            $reader_book_name = "";
            $reader_book_src = "";
            if ($book->toc_id){
                $toc = Toc::findOne($book->toc_id);
                $toc_href = $toc->app_href;
            }
            if ($book->reader_book_id){
                $reader_book = ReaderBook::findOne($book->reader_book_id);
                $reader_book_name = $reader_book->name;
                $reader_book_src = $reader_book->file_src;
            }
            array_push($books_arr, [
                'id' => $book->id,
                'name' => $book->name,
                'description' => $book->description,
                'file_src' => $book->file_src,
                'reader_book_id' => $book->reader_book_id,
                'toc_id' => $book->toc_id,
                'toc_href' => $toc_href,
                'reader_book_name' => $reader_book_name,
                'reader_book_src' => $reader_book_src,
            ]);
        }
        return json_encode($books_arr, JSON_UNESCAPED_UNICODE);
    }

    public function actionGetAudioFile($id){
        $book = Audiofile::findOne($id);
        // var_dump($book);die();
        $filename = explode('/', $book->file_src);
        $filename = $filename[2];
        if (!is_file("$book->file_src")) {
            throw new \yii\web\NotFoundHttpException('The file does not exists.');
        }
        // file_put_contents('test.txt', 'accessed//', FILE_APPEND);
        return Yii::$app->response->sendFile("$book->file_src", $filename);
        // return var_dump($book);
    }

    public function actionSetTocRelations($audiofile_id, $toc_id, $audio_book_id){
        $toc = Toc::findOne($toc_id);
        $reader_book = ReaderBook::find()->andWhere(['id' => $toc->book_id])->one();
        $reader_book_id = $reader_book->id;
        //если пришло обнуление, возьмем старый аудиофайл
        if ($audiofile_id == '0'){
            var_dump($toc->audiofile_id);
            $audiofile = Audiofile::findOne($toc->audiofile_id);
        }
        var_dump($audiofile);
        // var_dump($audiofile_id);
        // var_dump($toc_id);
        // var_dump($audio_book_id);
        // var_dump($reader_book_id);
        //действия для toc
        //если глава аудиокниги не указана - проставить null для toc
        if ($audiofile_id != '0'){
            //если раньше уже был указан аудиофайл и он меняется, то у старого нужно удалить связь.
            if ($toc->audiofile_id){
                $old_audiofile = Audiofile::findOne($toc->audiofile_id);
                $old_audiofile->reader_book_id = null;
                $old_audiofile->toc_id = null;                
                $old_audiofile->update();
            }
            $toc->audio_book_id = intval($audio_book_id);
            $toc->audiofile_id = intval($audiofile_id);
            if (!$audiofile){
                $audiofile = Audiofile::findOne($audiofile_id);
            }
            $audiofile->reader_book_id = intval($reader_book_id);
            $audiofile->toc_id = intval($toc_id);
        } else {
            $toc->audio_book_id = null;
            $toc->audiofile_id = null;
            $audiofile->reader_book_id = null;
            $audiofile->toc_id = null;
        }
        $toc->update();
        $audiofile->update();
    }
}