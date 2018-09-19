<?php

namespace app\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use app\models\AudioAuthor;
use app\models\AudioBook;
use app\models\UploadForm;
use app\models\Audiofile;
use yii\web\UploadedFile;


class AudioController extends Controller
{

    public $enableCsrfValidation = false;

    public function actionIndex(){
        return $this->render('index');
    }

    public function actionAuthors(){
        //если были данные с формы на добавление, добавим элемент
        $model = new AudioAuthor;
        if ($model->load(Yii::$app->request->post())){
            $model->save();
            $this->redirect("/audio/authors");
        }
        $authors = AudioAuthor::find()->all();
        return $this->render('authors', [
            'authors' => $authors,
        ]);
    }

    public function actionBooks(){
        //если были данные с формы на добавление, добавим элемент
        $model = new AudioBook;
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                $model->save();
                return $this->redirect('/audio/books');
                // return $this->refresh();
            }
        } else {
            $books = AudioBook::find()->all();
            return $this->render('books', [
                'books' => $books
            ]);
        }
    }

    public function actionEdit($id){
        //если были данные с формы на добавление, добавим элемент
        $model = AudioBook::findOne($id);
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect('/audio/books');
            }
        }
        return $this->render('edit', [
            'model' => $model,
            'uploadModel' => $uploadModel,
        ]);
    }

    public function actionEditAuthor($id){
        //если были данные с формы на добавление, добавим элемент
        $model = AudioAuthor::findOne($id);
        if ($model->load(Yii::$app->request->post())){
            $model->save();
            $this->redirect("/audio/authors");
        }
        return $this->render('edit-author', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id){
        $model = AudioBook::findOne($id);
        if ($model->delete()) {
            return $this->redirect('/audio/books');
        }
    }

    public function actionDeleteAuthor($id){
        $model = AudioAuthor::findOne($id);
        if ($model->delete()) {
            return $this->redirect('/audio/authors');
        }
    }

    public function actionRenderAudioList($book_id){
        $audiofiles = Audiofile::find()->andWhere(['audio_book_id' => $book_id])->all();
        return $this->renderAjax('render-audio-list', [
            'audiofiles' => $audiofiles,
        ]);
    }

    public function actionRenderAudioModal(){
        return $this->renderAjax('audio-modal');
    }
}