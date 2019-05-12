<?php
    use app\assets\AppAsset;
    use yii\helpers\Html;
    AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<html>
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, maximum-scale=1, user-scalable=no" /> 
        <meta name="theme-color" content="#202020">
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