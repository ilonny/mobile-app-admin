<?php

namespace app\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use app\models\AudioAuthor;
use app\models\ReaderBook;
use app\models\UploadForm;
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
        $model = new ReaderBook();
        $uploadModel = new UploadForm();
        if (Yii::$app->request->isPost) {
            $uploadModel->file = UploadedFile::getInstance($uploadModel, 'file');
            if ($uploadModel->file && $uploadModel->validate() &&  $uploadModel->file->extension == 'epub') {
                $fileName = 'uploads/' . $uploadModel->file->baseName . '_' . uniqid() . '.' . $uploadModel->file->extension;
                if ($uploadModel->file->saveAs($fileName)) {
                    $model->file_src = $fileName;
                }
            }

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect('/reader/books');
            }
        }
        $books = ReaderBook::find()->all();

        return $this->render('books', [
            'books' => $books,
            'model' => $model,
            'uploadModel' => $uploadModel,
        ]);
    }

    public function actionEdit($id){
        //если были данные с формы на добавление, добавим элемент
        $model = ReaderBook::findOne($id);
        $uploadModel = new UploadForm();
        if (Yii::$app->request->isPost) {
            $uploadModel->file = UploadedFile::getInstance($uploadModel, 'file');
            // var_dump($uploadModel);die();
            if ($uploadModel->file /* && $uploadModel->validate()  &&  $uploadModel->file->extension == 'epub' */) {
                $fileName = 'uploads/' . $uploadModel->file->baseName . '_' . uniqid() . '.' . $uploadModel->file->extension;
                if ($uploadModel->file->saveAs($fileName)) {
                    $model->file_src = $fileName;
                }
            }

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect('/reader/books');
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
}