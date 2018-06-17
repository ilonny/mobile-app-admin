<?php
namespace app\helpers;

use yii\web\ForbiddenHttpException;

/**
 * Class UploadedFile
 * @package common\components
 */
class UploadedFile extends \yii\web\UploadedFile
{
    /**
     * @var
     */
    public $pathToFile;

    /**
     * @var
     */
    private $base_path;

    /**
     * @var
     */
    public $relative_path;
    /**
     * @var
     */
    public $absolute_path;

    /**
     * @var array
     */
    public $allowedMimeTypes = ['image/jpg', 'image/png', 'image/jpeg'];
    /**
     * @var array
     */
    public $allowedExtensions = ['jpg', 'png', 'jpeg'];


    /**
     *  Загруженный файл изображение или нет
     */
    public function isImage()
    {
        return in_array($this->type, $this->allowedMimeTypes) && in_array(strtolower($this->extension), $this->allowedExtensions);
    }

    public function init()
    {
        $this->base_path = dirname(dirname(\Yii::$app->basePath));
        parent::init();
    }


    /**
     * @param $relativePath
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function saveImageAs($relativePath)
    {
        if ($this->isImage()) {
//            $this->pathToFile = FileHelper::generateFilePath($this->base_path, $relativePath, $this->extension);
            return $this->saveAs(FileHelper::normalizePath($this->base_path . '/' . $this->pathToFile));
        }
        throw new ForbiddenHttpException('Тип файла не разрешен');
    }

    /**
     * @param string $name
     * @return UploadedFile
     */
    public static function getInstanceByName($name)
    {
        return parent::getInstanceByName($name);
    }

    /**
     *
     */
    public function getRelativeUrl()
    {
        return $this->pathToFile;
    }

    /**
     * @return string
     */
    public function getAbsoluteUrl()
    {
        return \Yii::$app->urlManager->hostInfo . $this->pathToFile;
    }

    public function saveToServer()
    {
        $this->pathToFile = FileHelper::generateDeepFilePathNoExt($this->base_path, '/images/files/') . '.' . strtolower($this->extension);
        return $this->saveImageAs(FileHelper::normalizePath($this->base_path . '/' . $this->pathToFile));
    }

}