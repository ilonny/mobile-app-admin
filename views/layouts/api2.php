<?php
    use app\assets\AppAsset;
    use yii\helpers\Html;
    AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta name="theme-color" content="#202020">
        <meta name="viewport" content="width=device-width, maximum-scale=1, user-scalable=no" /> 
        <?= Html::encode($this->title) ?>
        <link href="/css/api.css?v=1.3" rel="stylesheet" async>
    </head>
    <body>
        <div class="container">
            <?= $content; ?>
        </div>
    </body>
</html>
<?php $this->endPage() ?>