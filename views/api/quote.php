<style>
    body{
        margin: 0;
        background-color: #efefef;
        font-size: 16px !important;
    }
    body p {
        font-size: 16px !important;
    }
    .container{
        padding: 0;
    }
    .body{
        padding: 10px 20px;
        background-color: #efefef;
    }
    .block{
        background-color: #fff;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0px 0px 10px 1px rgba(0,0,0,0.1);
    }
</style>
<?php
switch ($lang) {
    case 'ru':
        $title = $model->title;
        $text = $model->text;
    break;
    case 'eng':
        $title = $model->title_eng;
        $text = $model->text_eng;
    break;
    case 'en':
        $title = $model->title_eng;
        $text = $model->text_eng;
    break;
    case 'es':
        $title = $model->title_es;
        $text = $model->text_es;
    break;
    default:
        # code...
        break;
}
?>
<div class="body">
    <div class="block">
        <h3 class="title"><?= $title; ?></h3>
        <div class="img-wrap">
            <img src="/uploads/<?= $model->img_src; ?>" >
        </div>
        <div class="content"><?= $text;?></div>
    </div>
</div>