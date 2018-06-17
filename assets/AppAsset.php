<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css',
        'js/plugins/gre.css',
        'https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap.min.css',
        '//cdn.datatables.net/1.10.18/css/jquery.dataTables.min.css',
    ];
    public $js = [
        'js/plugins/jquery.gre.js',
        'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js',
        'js/jquery.image-uploader.js',
        'https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js',
        
        'js/quotes.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
