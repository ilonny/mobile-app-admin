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
        // phpinfo();
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
        $audiofiles = Audiofile::find()->andWhere(['audio_book_id' => $book_id])->orderBy('sort')->all();
        return $this->renderAjax('render-audio-list', [
            'audiofiles' => $audiofiles,
        ]);
    }

    public function actionRenderAudioModal(){
        return $this->renderAjax('audio-modal');
    }

    public function actionUploadAudio(){
        set_time_limit(300);
        ini_set('max_execution_time', 300);
        ini_set('post_max_size', "100M");
        $form_data = Yii::$app->request->post();
        // var_dump($_FILES);die();
        $file_src = 'uploads/audio/' . $_FILES['file']['name'];
        $file_src = explode('.', $file_src);
        $file_src = $file_src[0].'_'.time().'.'.$file_src[1];
        // var_dump($file_src);die();
        if (move_uploaded_file($_FILES['file']['tmp_name'], $file_src)){
            $max_sort = Audiofile::find()->max('sort');
            $model = new Audiofile;
            $model->name = $form_data['name'];
            $model->description = $form_data['description'];
            $model->file_src = $file_src;
            $model->audio_book_id = $form_data['book_id'];
            $model->sort = $max_sort + 1;
            if ($model->save()){
                return json_encode(['status' => 200, 'message' => 'Успешно сохранено'], JSON_UNESCAPED_UNICODE);
            }
        }
        return json_encode(['status' => 500, 'message' => "Возникла внутренняя ошибка сервера ${file_src}"], JSON_UNESCAPED_UNICODE);        
    }

    public function actionSaveSort(){
        $form_data = Yii::$app->request->post();
        $model = Audiofile::findOne($form_data['audio_id']);
        $model->sort = $form_data['sort_val'];
        if ($model->update()) return true;
    }

    public function actionDeleteAudio($audio_id){
        $model = Audiofile::findOne($audio_id);
        if ($model->delete()) return true;
    }
}