<style>
    body{
        margin: 0;
        background-color: #fafafa;
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
    audio{
        width: 100%;
        margin: 10px 0;
    }
</style>
<audio controls>
    <source src="/<?= $model->file_src; ?>" type="audio/mpeg">
</audio>