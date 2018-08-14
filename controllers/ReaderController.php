<?php

namespace app\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use app\models\ReaderAuthor;


class ReaderController extends Controller
{

    public $enableCsrfValidation = false;
    
    public function actionIndex(){
        return $this->render('index');
    }

    public function actionAuthors(){
        //если были данные с формы на добавление, добавим элемент
        $model = new ReaderAuthor;
        if ($model->load(Yii::$app->request->post())){
            $model->save();
            $this->redirect("/reader/authors");
        }
        $authors = ReaderAuthor::find()->all();
        return $this->render('authors', [
            'authors' => $authors,
        ]);
    }

    public function actionBooks(){
        //если были данные с формы на добавление, добавим элемент
        // $model = new ReaderAuthor;
        // if ($model->load(Yii::$app->request->post())){
        //     $model->save();
        //     $this->redirect("/reader/authors");
        // }
        // $authors = ReaderAuthor::find()->all();
        return $this->render('books', [
            // 'authors' => $authors,
        ]);
    }

}