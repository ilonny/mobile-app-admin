<?php

namespace app\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use app\models\ReaderAuthor;
use app\models\ReaderBook;
use app\models\UploadForm;
use app\models\Toc;
use app\models\AudioBook;
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
                // return $this->redirect('/reader/books');
                //copy epub to zip and unzi p it for get tocs of book
                $fileName = explode('.', $fileName);
                copy($fileName[0].'.epub', $fileName[0].'.zip');
                $zip = new \ZipArchive;
                $zip->open($fileName[0].'.zip');
                $zip->extractTo($fileName[0]);
                $zip->close();
                $xml = simplexml_load_file($fileName[0].'/toc.ncx');
                foreach ($xml->{"navMap"}->{"navPoint"} as $toc){
                    $toc_model = new Toc();
                    $toc_model->app_href = $toc->content['src']->__toString();
                    $toc_model->title = $toc->navLabel->text->__toString();
                    $toc_model->book_id = $model->id;
                    $toc_model->save();
                }
            }
        }
        $books = ReaderBook::find()->all();
        return $this->render('books', [
            'books' => $books,
            'uploadModel' => $uploadModel,
        ]);
    }

    public function actionEdit($id){
        //если были данные с формы на добавление, добавим элемент
        $model = ReaderBook::findOne($id);
        $uploadModel = new UploadForm();
        $audioBooks = AudioBook::find()->all();
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
            'audioBooks' => $audioBooks,
        ]);
    }

    public function actionEditAuthor($id){
        //если были данные с формы на добавление, добавим элемент
        $model = ReaderAuthor::findOne($id);
        if ($model->load(Yii::$app->request->post())){
            $model->save();
            $this->redirect("/reader/authors");
        }
        return $this->render('edit-author', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id){
        $model = ReaderBook::findOne($id);
        if ($model->delete()) {
            return $this->redirect('/reader/books');
        }
    }

    public function actionDeleteAuthor($id){
        $model = ReaderAuthor::findOne($id);
        if ($model->delete()) {
            return $this->redirect('/reader/authors');
        }
    }
}