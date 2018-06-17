<?php
namespace app\helpers;



/**
 */
class FileHelper extends \yii\helpers\FileHelper
{
    public static function getRandomFileName($path, $extension = '', $fullPath = false)
    {
        $extension = $extension ? '.' . $extension : '';
        $path = $path ? $path . '/' : '';

        do {
            $name = md5(microtime() . rand(0, 9999));
            $file = $path . $name . $extension;
        } while (file_exists($file));

        return ($fullPath === false) ? $name : $file;
    }

    public static function getRandomFileNameWithPath($path, $extension = '', $host = '')
    {
        $extension = $extension ? '.' . $extension : '';
        $path = $path ? $path . '/' : '';

        do {
            $name = md5(microtime() . rand(0, 9999));
            $file = $path . $name . $extension;
        } while (file_exists($host . $file));

        return $file;
    }

    public static function resizeImage($sourceImagePath)
    {
        $newWidth = 1024;
        $newHeight = 768;
        list($imageWidth, $imageHeight) = getimagesize($sourceImagePath);
        if (($imageWidth <= $newWidth) && ($imageHeight <= $newHeight)) {
            return true;
        }
        if (mime_content_type($sourceImagePath) == 'image/jpeg') {
            $image = imagecreatefromjpeg($sourceImagePath);
        } elseif (mime_content_type($sourceImagePath) == 'image/png') {
            $image = imagecreatefrompng($sourceImagePath);
        } else {
            return false;
        }

        $sourceRatio = $imageWidth / $imageHeight;

        if ($imageWidth < $imageHeight) {
            //тогда высота новое изображение подходит по ширине, но не подходит по высоте (новое меньше), значит обрезаем по высоте
            $newTempHeight = $newHeight;
            $newTempWidth = $newHeight * $sourceRatio;
        } else {
            //тогда высота нового изображения подходит, но ширина нового, меньше, значит обрезаем по ширине
            $newTempWidth = $newWidth;
            $newTempHeight = (int)($newWidth / $sourceRatio);
        }

        $dest_r = imagecreatetruecolor($newTempWidth, $newTempHeight);
        /******* Add these 2 lines maintain transparency of PNG images ****/
        imagealphablending($dest_r, false);
        imagesavealpha($dest_r, true);
        $transparent = imagecolorallocatealpha($dest_r, 255, 255, 255, 127);
        imagefilledrectangle($dest_r, 0, 0, $newTempWidth, $newTempHeight, $transparent);
        /********end*********/

        if (!imagecopyresampled($dest_r, $image, 0, 0, 0, 0, $newTempWidth, $newTempHeight, $imageWidth, $imageHeight)) {
            return false;
        }
        // save only png or jpeg pictures
        if (mime_content_type($sourceImagePath) == 'image/jpeg') {
            imagejpeg($dest_r, $sourceImagePath, 81);
        } elseif (mime_content_type($sourceImagePath) == 'image/png') {
            imagepng($dest_r, $sourceImagePath);
        }
        return true;

    }

    public static function cropImageWithWaterMark($sourceImagePath, $fromX = 0, $fromY = 0, $width = 0, $height = 0, $defaultWidth = 0, $defaultHeight = 0)
    {
        if (mime_content_type($sourceImagePath) == 'image/jpeg') {
            $image = imagecreatefromjpeg($sourceImagePath);
        } elseif (mime_content_type($sourceImagePath) == 'image/png') {
            $image = imagecreatefrompng($sourceImagePath);
        } else {
            return false;
        }

        $dest_r = imagecreatetruecolor($defaultWidth, $defaultHeight);
        /******* Add these 2 lines maintain transparency of PNG images ****/
        imagealphablending($dest_r, false);
        imagesavealpha($dest_r, true);
        $transparent = imagecolorallocatealpha($dest_r, 255, 255, 255, 127);
        imagefilledrectangle($dest_r, 0, 0, $defaultWidth, $defaultHeight, $transparent);
        /********end*********/

        if (!imagecopyresampled($dest_r, $image, 0, 0, $fromX, $fromY, $defaultWidth, $defaultHeight, $width, $height)) {
            return false;
        }

        // Сначала создаем наше изображение штампа вручную с помощью GD
        $stamp = imagecreatefrompng(\Yii::getAlias('@backend/web') . '/img/watermark.png');

        // Установка полей для штампа и получение высоты/ширины штампа
        $marge_right = 10;
        $marge_top = 10;
        $sx = 74;
        $sy = 65;

        imagealphablending($stamp, true);
        imagealphablending($dest_r, true);

        // Слияние штампа с фотографией
        imagecopy($dest_r, $stamp, $defaultWidth - $sx - $marge_right, $marge_top, 0, 0, $sx, $sy);

        // save only png or jpeg pictures
        if (mime_content_type($sourceImagePath) == 'image/jpeg') {
            imagejpeg($dest_r, $sourceImagePath, 81);
        } elseif (mime_content_type($sourceImagePath) == 'image/png') {
            imagepng($dest_r, $sourceImagePath);
        }
        return true;
    }

    public static function cropImage($sourceImagePath, $fromX = 0, $fromY = 0, $width = 0, $height = 0, $defaultWidth = 0, $defaultHeight = 0)
    {
        if (mime_content_type($sourceImagePath) == 'image/jpeg') {
            $image = imagecreatefromjpeg($sourceImagePath);
        } elseif (mime_content_type($sourceImagePath) == 'image/png') {
            $image = imagecreatefrompng($sourceImagePath);
        } else {
            return false;
        }

        $dest_r = imagecreatetruecolor($defaultWidth, $defaultHeight);
        /******* Add these 2 lines maintain transparency of PNG images ****/
        imagealphablending($dest_r, false);
        imagesavealpha($dest_r, true);
        $transparent = imagecolorallocatealpha($dest_r, 255, 255, 255, 127);
        imagefilledrectangle($dest_r, 0, 0, $defaultWidth, $defaultHeight, $transparent);
        /********end*********/

        if (!imagecopyresampled($dest_r, $image, 0, 0, $fromX, $fromY, $defaultWidth, $defaultHeight, $width, $height)) {
            return false;
        }
        // save only png or jpeg pictures
        if (mime_content_type($sourceImagePath) == 'image/jpeg') {
            imagejpeg($dest_r, $sourceImagePath, 100);
        } elseif (mime_content_type($sourceImagePath) == 'image/png') {
            imagepng($dest_r, $sourceImagePath);
        }
        return true;
    }

    public static function grayscaleImage($filename)
    {
        //Получаем размеры изображения
        $img_size = getimagesize($filename);
        $width = $img_size[0];
        $height = $img_size[1];
        //Создаем новое изображение с такмими же размерами
        $img = imagecreate($width, $height);
        //Задаем новому изображению палитру "оттенки серого" (grayscale)
        for ($c = 0; $c < 256; $c++) {
            imagecolorallocate($img, $c, $c, $c);
        }
        //Содаем изображение из файла
        if (mime_content_type($filename) == 'image/jpeg') {
            $img2 = imagecreatefromjpeg($filename);
        } elseif (mime_content_type($filename) == 'image/png') {
            $img2 = imagecreatefrompng($filename);
        } else {
            return false;
        }
        //Объединяем два изображения
        imagecopymerge($img, $img2, 0, 0, 0, 0, $width, $height, 100);
        //Сохраняем полученное изображение
        if (mime_content_type($filename) == 'image/jpeg') {
            imagejpeg($img, $filename, 100);
        } elseif (mime_content_type($filename) == 'image/png') {
            imagepng($img, $filename);
        }
        return true;
    }

    /**
     * Генерирует тумб для изображения с наложением водяного знака
     * @param $file_path
     * @param $width
     * @param $height
     * @return string
     * @throws \Exception
     * @throws \yii\base\Exception
     */
    public static function getImageThumb($file_path, $width, $height)
    {
        $base_path = \Yii::getAlias('@webroot');
        $absolute_path_to_original = $base_path . DIRECTORY_SEPARATOR . $file_path;
        //всегда пытаемся создать папку
        if (!self::createDirectory(self::normalizePath($base_path . self::normalizePath(dirname($file_path) . '/thumbs')))) {
            return self::getNoPhotoImage($width, $height);
        }
        $preview_path = dirname($file_path) . '/thumbs/thumb_' . pathinfo($file_path, PATHINFO_FILENAME) . '_' . $width . 'x' . $height . '.' . self::getExtensionByFileName($absolute_path_to_original);
        $absolute_path_to_preview = $base_path . $preview_path;

        //если нет доступа к оригиналу файла, то выводим картинку, идзображение отсутствует
        if (!file_exists(self::normalizePath($absolute_path_to_original))) {
            return self::getNoPhotoImage($width, $height);
        }
        //если файл еще не был создан, то генерируем
        if (!file_exists($absolute_path_to_preview)) {
            if (!(is_file($absolute_path_to_original) && copy($absolute_path_to_original, $absolute_path_to_preview))) {
                return self::getNoPhotoImage($width, $height);
            }
            if (!self::getThumbFromImage($absolute_path_to_preview, $width, $height) && self::addWaterMark($absolute_path_to_preview)) {
                return self::getNoPhotoImage($width, $height);
            }
        }
        return $preview_path;
    }

    /**
     * Генерирует тумб отсутствующего изображения. Оригинал файла лежит в /common/files/no_photo.jpg
     * @param $width
     * @param $height
     * @return string
     * @throws \Exception
     * @throws \yii\base\Exception
     */
    public static function getNoPhotoImage($width, $height)
    {
        self::createDirectory(\Yii::getAlias('@app') . '/web/assets/image_thumbs/');
        $no_photo_image_path = \Yii::getAlias('@common') . '/files/noimage.png';
        $preview_url = '/assets/image_thumbs/' . $width . 'x' . $height . '.' . self::getExtensionByFileName($no_photo_image_path);
        $preview_url_path = \Yii::getAlias('@webroot') . $preview_url;
        if (!file_exists($preview_url_path)) {
            copy($no_photo_image_path, $preview_url_path);
            self::getThumbFromImage($preview_url_path, $width, $height);
        }
        return $preview_url;
    }

    public static function getExtensionByFileName($file)
    {
        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }

    public static function getThumbFromImage($sourceImagePath, $newWidth, $newHeight)
    {
        if (($newHeight * $newWidth) <= 0) {
            throw new \Exception('Неверные ширина и/или высота для preview');
        }
        if (mime_content_type($sourceImagePath) == 'image/jpeg') {
            $image = imagecreatefromjpeg($sourceImagePath);
        } elseif (mime_content_type($sourceImagePath) == 'image/png') {
            $image = imagecreatefrompng($sourceImagePath);
        } else {
            return false;
        }
        //Сохраняем прозрачность у PNG
        $destinationResource = imagecreatetruecolor($newWidth, $newHeight);
        /******* Add these 2 lines maintain transparency of PNG images ****/
        imagealphablending($destinationResource, false);
        imagesavealpha($destinationResource, true);
        $transparent = imagecolorallocatealpha($destinationResource, 255, 255, 255, 127);
        imagefilledrectangle($destinationResource, 0, 0, $newWidth, $newHeight, $transparent);
        /********end*********/

        list($imageWidth, $imageHeight) = getimagesize($sourceImagePath);

        $destRatio = $newWidth / $newHeight;

        if ($imageWidth / $destRatio < $imageHeight) {
            //тогда высота новое изображение подходит по ширине, но не подходит по высоте (новое меньше), значит обрезаем по высоте
            $newTempWidth = (int)$imageWidth;
            $newTempHeight = (int)($imageWidth / $destRatio);
            $fromX = 0;
            $fromY = (int)(($imageHeight - $imageWidth / $destRatio) / 2);
        } else {
            //тогда высота нового изображения подходит, но ширина нового, меньше, значит обрезаем по ширине
            $newTempWidth = (int)($imageHeight * $destRatio);
            $newTempHeight = $imageHeight;
            $fromX = (int)(($imageWidth - $imageHeight * $destRatio) / 2);
            $fromY = 0;
        }

        if (!imagecopyresampled($destinationResource, $image, 0, 0, $fromX, $fromY, $newWidth, $newHeight, $newTempWidth, $newTempHeight)) {
            return false;
        }
        // save only png or jpeg pictures
        if (mime_content_type($sourceImagePath) == 'image/jpeg') {
            imagejpeg($destinationResource, $sourceImagePath, 100);
        } elseif (mime_content_type($sourceImagePath) == 'image/png') {
            imagepng($destinationResource, $sourceImagePath);
        }
        return true;
    }

    public static function addWaterMark($sourceImagePath)
    {
        try {
            if (mime_content_type($sourceImagePath) == 'image/jpeg') {
                $image = imagecreatefromjpeg($sourceImagePath);
            } elseif (mime_content_type($sourceImagePath) == 'image/png') {
                $image = imagecreatefrompng($sourceImagePath);
            } else {
                return false;
            }

            list($imageWidth, $imageHeight) = getimagesize($sourceImagePath);

            // Сначала создаем наше изображение штампа вручную с помощью GD
            $watermarkPath = \Yii::getAlias('@backend/web') . '/img/watermark.png';
            $stamp = imagecreatefrompng($watermarkPath);

            list($watermarkWidth, $watermarkHeight) = getimagesize($watermarkPath);
            $watermarkAspect = $watermarkWidth / $watermarkHeight;
            $imageAspect = $imageWidth / $imageHeight;

            if ($watermarkAspect < $imageAspect) {
                //по высоте впритык
                $newWatermarkHeight = $imageHeight / 3 * 2;
                $marginTop = $imageHeight / 6;

                $newWatermarkWidth = $newWatermarkHeight * $watermarkAspect;
                $marginLeft = (int)($imageWidth - $newWatermarkWidth) / 2;
            } else {
                //по высоте впритык
                $newWatermarkWidth = (int)$imageWidth / 3 * 2;
                $marginLeft = (int)$imageWidth / 6;

                $newWatermarkHeight = $newWatermarkWidth / $watermarkAspect;
                $marginTop = (int)($imageHeight - $newWatermarkHeight) / 2;
            }
            $newWatermark = imagecreatetruecolor($newWatermarkWidth, $newWatermarkHeight);
            imagealphablending($newWatermark, false);
            imagesavealpha($image, true);
            imagecopyresampled($newWatermark, $stamp, 0, 0, 0, 0, $newWatermarkWidth, $newWatermarkHeight, $watermarkWidth, $watermarkHeight);
            imagesavealpha($image, true);
            // Слияние штампа с фотографией
            imagecopy($image, $newWatermark, $marginLeft, $marginTop, 0, 0, $newWatermarkWidth, $newWatermarkHeight);

            // save only png or jpeg pictures
            if (mime_content_type($sourceImagePath) == 'image/jpeg') {
                imagejpeg($image, $sourceImagePath, 81);
            } elseif (mime_content_type($sourceImagePath) == 'image/png') {
                imagepng($image, $sourceImagePath);
            }
            return true;
        } catch (\Exception $e) {
            \Yii::error('Ошибка добавлении водяного знака: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * @param $basePath
     * @param $relativePath
     * @param $extension
     * @return string
     * @throws \yii\base\Exception
     */
    public static function generateFilePath($basePath, $relativePath, $extension)
    {
        self::createDirectory(self::normalizePath($basePath . '/' . $relativePath));
        do {
            $filePath = self::normalizePath($relativePath . '/' . md5(microtime() . rand(0, 9999)) . '.' . $extension, '/');
        } while (file_exists(self::normalizePath($basePath . '/' . $filePath)));
        return $filePath;
    }

    /**
     * Генерируем путь к файлу без расширений, с созданием случайного подкаталога вложенности $level
     * @param $basePath
     * @param $relativePath
     * @param int $level
     * @return string
     * @throws \yii\base\Exception
     */
    public static function generateDeepFilePathNoExt($basePath, $relativePath, $level = 2)
    {
        $lengthSubFolder = 5;
        do {
            $sub_folders = [];
            for ($i = 0; $i < $level; $i++) {
                $sub_folders[] = substr(md5(microtime() . rand(0, 9999)), 0, $lengthSubFolder);
            }
            $filePath = self::normalizePath($relativePath . '/' . implode('/', $sub_folders) . '/' . md5(microtime() . rand(0, 9999)), '/');
        } while (file_exists(self::normalizePath($basePath . '/' . $filePath)));

        self::createDirectory(self::normalizePath($basePath . '/' . dirname($filePath)));
        return $filePath;
    }
}
