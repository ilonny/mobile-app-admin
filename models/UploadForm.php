<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;
    public $file_eng;
    public $file_es;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'maxSize' => 1024 * 1024 * 100],
            [['file_eng'], 'file', 'maxSize' => 1024 * 1024 * 100],
            [['file_es'], 'file', 'maxSize' => 1024 * 1024 * 100],
        ];
    }
}

?>