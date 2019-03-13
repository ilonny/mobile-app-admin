<?php

/* @var $this yii\web\View */
use yii\helpers\Url;
$this->title = 'Mobile app Admin Panel';
?>
<div class="site-index">
    <div class="jumbotron">
        <h1>Добро пожаловать!</h1>
        <p class="lead">Выберите категорию для редактирования</p>
        <p><a class="btn btn-lg btn-success" href="mailto::hello@fflames.ru">Задать вопрос разработчикам</a></p>
    </div>
    <div class="body-content">
        <div class="row">
            <div class="col-lg-4 col-xs-12">
                <h2>Авторы </h2>
                <p>Добавление и редактирование авторов (для цитат)</p>
                <p><a class="btn btn-default" href="<?= Url::to(['site/list', 'type' => 'author']); ?>">Перейти</a></p>
            </div>
            <div class="col-lg-4 col-xs-12">
                <h2>Книги </h2>
                <p>Добавление и редактирование книг (для цитат)</p>
                <p><a class="btn btn-default" href="<?= Url::to(['site/list', 'type' => 'book']); ?>">Перейти</a></p>
            </div>
            <div class="col-lg-4 col-xs-12">
                <h2>Цитаты</h2>
                <p>Добавление и редактирование цитат</p>
                <p><a class="btn btn-default" href="<?= Url::to(['site/quotes']); ?>">Перейти</a></p>
            </div>
            <div class="col-lg-4 col-xs-12">
                <h2>Push - Уведомления</h2>
                <p>Создание push-уведомлений, просмотр истории уведомлений</p>
                <p><a class="btn btn-default" href="<?= Url::to(['site/push']); ?>">Перейти</a></p>
            </div>
            <div class="col-lg-4 col-xs-12">
                <h2>Epub читалка</h2>
                <p>Редактирование авторов, редактирование книг</p>
                <p><a class="btn btn-default" href="<?= Url::to(['reader/index']); ?>">Перейти</a></p>
            </div>
            <div class="col-lg-4 col-xs-12">
                <h2>Аудиокниги</h2>
                <p>Редактирование авторов, редактирование аудиокниг</p>
                <p><a class="btn btn-default" href="<?= Url::to(['audio/index']); ?>">Перейти</a></p>
            </div>
        </div>
    </div>
</div>
