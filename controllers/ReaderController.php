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
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
        return $this->render('index');
    }

    public function actionAuthors(){
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
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
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
        //если были данные с формы на добавление, добавим элемент
        $model = new ReaderBook();
        $need_tocs = false;
        $need_tocs_eng = false;
        $need_tocs_es = false;
        if (!$model->file_src){
            $need_tocs = true;
        }
        if (!$model->file_src_eng){
            $need_tocs_eng = true;
        }
        if (!$model->file_src_es){
            $need_tocs_es = true;
        }
        $uploadModel = new UploadForm();
        $uploadModelEng = new UploadForm();
        $uploadModelEs = new UploadForm();
        if (Yii::$app->request->isPost) {
            $uploadModel->file = UploadedFile::getInstance($uploadModel, 'file');
            if ($uploadModel->file && $uploadModel->validate() &&  $uploadModel->file->extension == 'epub') {
                $fileName = 'uploads/' . $uploadModel->file->baseName . '_' . uniqid() . '.' . $uploadModel->file->extension;
                if ($uploadModel->file->saveAs($fileName)) {
                    $model->file_src = $fileName;
                }
            }
            //
            $uploadModelEng->file = UploadedFile::getInstance($uploadModel, 'file_eng');
            if ($uploadModelEng->file) {
                $fileNameEng = 'uploads/' . $uploadModelEng->file->baseName . '_eng_' . uniqid() . '.' . $uploadModelEng->file->extension;
                if ($uploadModelEng->file->saveAs($fileNameEng)) {
                    $model->file_src_eng = $fileNameEng;
                }
            }
            //
            $uploadModelEs->file = UploadedFile::getInstance($uploadModel, 'file_es');
            if ($uploadModelEs->file) {
                $fileNameEs = 'uploads/' . $uploadModelEs->file->baseName . '_es_' . uniqid() . '.' . $uploadModelEs->file->extension;
                if ($uploadModelEs->file->saveAs($fileNameEs)) {
                    $model->file_src_es = $fileNameEs;
                }
            }
            //
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                //copy epub to zip and unzi p it for get tocs of book
                if ($uploadModel->file) {
                    $fileName = explode('.', $fileName);
                    copy($fileName[0].'.epub', $fileName[0].'.zip');
                    $zip = new \ZipArchive;
                    $zip->open($fileName[0].'.zip');
                    $zip->extractTo($fileName[0]);
                    $zip->close();
                    $files=\yii\helpers\FileHelper::findFiles($fileName[0]);
                    foreach ($files as $file_path){
                        $file_path_arr = explode('/', $file_path);
                        if ($file_path_arr[count($file_path_arr)-1] == 'toc.ncx'){
                            $toc_path = $file_path;
                        }
                    }
                    $xml = simplexml_load_file($toc_path);
                    foreach ($xml->{"navMap"}->{"navPoint"} as $toc){
                        $toc_model = new Toc();
                        $toc_model->app_href = $toc->content['src']->__toString();
                        $toc_model->title = $toc->navLabel->text->__toString();
                        $toc_model->book_id = $model->id;
                        $toc_model->save();
                    }
                }
                ///////
                /////// todo 
                /////// протестить создание книги на генерацию глав для англ версии
                ///////
                //
                //код по записи глав для англ аудиокниги
                //copy epub to zip and unzi p it for get tocs of book
                if ($uploadModelEng->file) {
                    $fileName = $fileNameEng;
                    $fileName = explode('.', $fileName);
                    copy($fileName[0].'.epub', $fileName[0].'.zip');
                    $zip = new \ZipArchive;
                    $zip->open($fileName[0].'.zip');
                    $zip->extractTo($fileName[0]);
                    $zip->close();
                    $files=\yii\helpers\FileHelper::findFiles($fileName[0]);
                    foreach ($files as $file_path){
                        $file_path_arr = explode('/', $file_path);
                        if ($file_path_arr[count($file_path_arr)-1] == 'toc.ncx'){
                            $toc_path = $file_path;
                        }
                    }
                    $xml = simplexml_load_file($toc_path);
                    foreach ($xml->{"navMap"}->{"navPoint"} as $toc){
                        $toc_model = new Toc();
                        $toc_model->app_href = $toc->content['src']->__toString();
                        $toc_model->title = $toc->navLabel->text->__toString();
                        $toc_model->book_id = $model->id;
                        $toc_model->other = 'eng';
                        $toc_model->save();
                    }
                }
                //
                if ($uploadModelEs->file) {
                    $fileName = $fileNameEs;
                    $fileName = explode('.', $fileName);
                    copy($fileName[0].'.epub', $fileName[0].'.zip');
                    $zip = new \ZipArchive;
                    $zip->open($fileName[0].'.zip');
                    $zip->extractTo($fileName[0]);
                    $zip->close();
                    $files=\yii\helpers\FileHelper::findFiles($fileName[0]);
                    foreach ($files as $file_path){
                        $file_path_arr = explode('/', $file_path);
                        if ($file_path_arr[count($file_path_arr)-1] == 'toc.ncx'){
                            $toc_path = $file_path;
                        }
                    }
                    $xml = simplexml_load_file($toc_path);
                    foreach ($xml->{"navMap"}->{"navPoint"} as $toc){
                        $toc_model = new Toc();
                        $toc_model->app_href = $toc->content['src']->__toString();
                        $toc_model->title = $toc->navLabel->text->__toString();
                        $toc_model->book_id = $model->id;
                        $toc_model->other = 'es';
                        $toc_model->save();
                    }
                }
                //
                return $this->redirect("/reader/edit?id={$model->id}");
            }
        }
        $books = ReaderBook::find()->all();
        return $this->render('books', [
            'books' => $books,
            'uploadModel' => $uploadModel,
        ]);
    }

    public function actionEdit($id){
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
        //если были данные с формы на добавление, добавим элемент
        $model = ReaderBook::findOne($id);
        $need_tocs = false;
        $need_tocs_eng = false;
        $need_tocs_es = false;
        if (!$model->file_src){
            $need_tocs = true;
        }
        if (!$model->file_src_eng){
            $need_tocs_eng = true;
        }
        if (!$model->file_src_es){
            $need_tocs_es = true;
        }
        $uploadModel = new UploadForm();
        $uploadModelEng = new UploadForm();
        $uploadModelEs = new UploadForm();
        $audioBooks = AudioBook::find()->all();
        if (Yii::$app->request->isPost) {
            $uploadModel->file = UploadedFile::getInstance($uploadModel, 'file');
            if ($uploadModel->file /* && $uploadModel->validate()  &&  $uploadModel->file->extension == 'epub' */) {
                $fileName = 'uploads/' . $uploadModel->file->baseName . '_' . uniqid() . '.' . $uploadModel->file->extension;
                if ($uploadModel->file->saveAs($fileName)) {
                    $model->file_src = $fileName;
                }
            }
            //
            $uploadModelEng->file = UploadedFile::getInstance($uploadModel, 'file_eng');
            if ($uploadModelEng->file) {
                $fileNameEng = 'uploads/' . $uploadModelEng->file->baseName . '_eng_' . uniqid() . '.' . $uploadModelEng->file->extension;
                if ($uploadModelEng->file->saveAs($fileNameEng)) {
                    $model->file_src_eng = $fileNameEng;
                }
            }
            //
            $uploadModelEs->file = UploadedFile::getInstance($uploadModel, 'file_es');
            if ($uploadModelEs->file) {
                $fileNameEs = 'uploads/' . $uploadModelEs->file->baseName . '_es_' . uniqid() . '.' . $uploadModelEs->file->extension;
                if ($uploadModelEs->file->saveAs($fileNameEs)) {
                    $model->file_src_es = $fileNameEs;
                }
            }
            //
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                //
                    if ($uploadModel->file) {
                        //код по записи глав для русской аудиокниги
                        $fileName = explode('.', $fileName);
                        copy($fileName[0].'.epub', $fileName[0].'.zip');
                        $zip = new \ZipArchive;
                        $zip->open($fileName[0].'.zip');
                        $zip->extractTo($fileName[0]);
                        $zip->close();
                        $files=\yii\helpers\FileHelper::findFiles($fileName[0]);
                        foreach ($files as $file_path){
                            $file_path_arr = explode('/', $file_path);
                            if ($file_path_arr[count($file_path_arr)-1] == 'toc.ncx'){
                                $toc_path = $file_path;
                            }
                        }
                        $xml = simplexml_load_file($toc_path);
                        \Yii::$app
                            ->db
                            ->createCommand("DELETE from toc where book_id = ${id} and other is NULL")
                            ->execute();
                        foreach ($xml->{"navMap"}->{"navPoint"} as $toc){
                            $toc_model = new Toc();
                            $toc_model->app_href = $toc->content['src']->__toString();
                            $toc_model->title = $toc->navLabel->text->__toString();
                            $toc_model->book_id = $model->id;
                            $toc_model->save();
                        }
                    }
                    if ($uploadModelEng->file) {
                        //код по записи глав для англ аудиокниги
                        //copy epub to zip and unzi p it for get tocs of book
                        $fileName = $fileNameEng;
                        $fileName = explode('.', $fileName);
                        copy($fileName[0].'.epub', $fileName[0].'.zip');
                        $zip = new \ZipArchive;
                        $zip->open($fileName[0].'.zip');
                        $zip->extractTo($fileName[0]);
                        $zip->close();
                        $files=\yii\helpers\FileHelper::findFiles($fileName[0]);
                        foreach ($files as $file_path){
                            $file_path_arr = explode('/', $file_path);
                            if ($file_path_arr[count($file_path_arr)-1] == 'toc.ncx'){
                                $toc_path = $file_path;
                            }
                        }
                        $xml = simplexml_load_file($toc_path);
                        \Yii::$app
                            ->db
                            ->createCommand()
                            ->delete('toc', ['book_id' => $id, 'other' => 'eng'])
                            ->execute();
                        foreach ($xml->{"navMap"}->{"navPoint"} as $toc){
                            $toc_model = new Toc();
                            $toc_model->app_href = $toc->content['src']->__toString();
                            $toc_model->title = $toc->navLabel->text->__toString();
                            $toc_model->book_id = $model->id;
                            $toc_model->other = 'eng';
                            $toc_model->save();
                        }
                    }
                    if ($uploadModelEs->file) {
                        //код по записи глав для англ аудиокниги
                        //copy epub to zip and unzi p it for get tocs of book
                        $fileName = $fileNameEs;
                        $fileName = explode('.', $fileName);
                        copy($fileName[0].'.epub', $fileName[0].'.zip');
                        $zip = new \ZipArchive;
                        $zip->open($fileName[0].'.zip');
                        $zip->extractTo($fileName[0]);
                        $zip->close();
                        $files=\yii\helpers\FileHelper::findFiles($fileName[0]);
                        foreach ($files as $file_path){
                            $file_path_arr = explode('/', $file_path);
                            if ($file_path_arr[count($file_path_arr)-1] == 'toc.ncx'){
                                $toc_path = $file_path;
                            }
                        }
                        $xml = simplexml_load_file($toc_path);
                        \Yii::$app
                            ->db
                            ->createCommand()
                            ->delete('toc', ['book_id' => $id, 'other' => 'es'])
                            ->execute();
                        foreach ($xml->{"navMap"}->{"navPoint"} as $toc){
                            $toc_model = new Toc();
                            $toc_model->app_href = $toc->content['src']->__toString();
                            $toc_model->title = $toc->navLabel->text->__toString();
                            $toc_model->book_id = $model->id;
                            $toc_model->other = 'es';
                            $toc_model->save();
                        }
                    }
                //
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
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
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
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
        $model = ReaderBook::findOne($id);
        if ($model->delete()) {
            return $this->redirect('/reader/books');
        }
    }

    public function actionDeleteAuthor($id){
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }
        $model = ReaderAuthor::findOne($id);
        if ($model->delete()) {
            return $this->redirect('/reader/authors');
        }
    }
}