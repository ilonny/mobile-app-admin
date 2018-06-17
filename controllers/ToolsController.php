<?php

namespace app\controllers;

use app\components\Controller;
use app\helpers\UploadedFile;
use Exception;
use yii\helpers\Json;

class ToolsController extends Controller
{

    public $enableCsrfValidation = false;

    public function actionUploadImage()
    {
        try {
            if ($uploadedFile = UploadedFile::getInstanceByName('image_files')) {
                if ($uploadedFile->saveToServer()) {
                    return Json::encode(array(
                        'preview' => $uploadedFile->getAbsoluteUrl(),
                        'filename' => $uploadedFile->getRelativeUrl(),
                    ));
                }
            }
        } catch (Exception $e) {
            return 'Ошибка при загрузке: ' .  $e->getMessage();
        }
        return 'Ошибка при загрузке';
    }

    public function actionDoc()
    {
        $file = $_FILES['file'];
        // var_dump($file);
        $uploaddir = DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR;
        $uniq_name = time().basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], \Yii::getAlias('@webroot'). '../../'. $uploaddir . $uniq_name)){
            return $uploaddir . $uniq_name;
        }
    }

}
