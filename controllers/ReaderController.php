<?php

namespace app\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use app\models\ReaderAuthor;
use app\models\ReaderBook;
use app\models\UploadForm;
use yii\web\UploadedFile;


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

    public function actionDelete($id){
        $model = ReaderBook::findOne($id);
        if ($model->delete()) {
            return $this->redirect('/reader/books');
        }
    }

}