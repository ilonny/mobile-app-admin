<style>
    body{
        margin: 0;
        background-color: #efefef;
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
<div class="body">
    <div class="block">
        <h3 class="title"><?= $model->title; ?></h3>
        <div class="img-wrap">
            <img src="/uploads/<?= $model->img_src; ?>" >
        </div>
        <div class="content"><?= $model->text;?></div>
    </div>
</div>