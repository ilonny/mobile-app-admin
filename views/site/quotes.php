<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;
    use yii\widgets\ActiveForm;
    use app\models\Quote;
    use app\models\Item;
    use kartik\file\FileInput;
?>
<div class="body-content">
    <div class="row">
        <h1>Список цитат</h1>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#list">Список</a>
                </li>
                <li>
                    <a data-toggle="tab" href="#add">Добавить цитату</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="list" class="tab-pane active fade in  list-group" style="padding:30px 0;">
                    <table class="table table-striped table-bordered" id="data-table" style="margin: 30px 0;">
                        <thead>
                            <tr>
                                <th>Заголовок</th>
                                <th>Источник</th>
                                <th>Тип источника</th>
                                <th>Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($quotes as $quote): ?>
                                <tr>
                                    <td><?= $quote->title; ?></td>
                                    <td><?= $quote->getAuthorName(); ?></td>
                                    <td><?= $quote->getAuthorType(); ?></td>
                                    <td>
                                        <?= Html::beginForm(['/site/delete-quote', 'id' => $quote->id], 'post'); ?>
                                        <?= Html::submitButton(
                                                'удалить',
                                                ['class' => 'btn btn-danger pull-right']
                                        )?>
                                        <?= Html::endForm(); ?>
                                        <a href="<?= Url::to(['site/edit-quote', 'id' => $quote->id]) ?>" class="btn btn-primary pull-right">Редактировать</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div id="add" class="tab-pane ">
                    <?php
                        $form = ActiveForm::begin();
                        $model = new Quote;
                    ?>
                    <p>Выберите одно изображение (или не выбирайте), после отображения превью изображения, просто заполните дальше форму, кнопку "загрузить" нажимать не нужно</p>
                    <?= $form->field($model, 'img_src')->widget(FileInput::classname(), [
                        'language' => 'ru',
                        'options' => ['accept' => 'image/*'],
                        // 'pluginOptions' => [
                        //     'uploadUrl' => Url::to(['site/upload']),
                        // ]
                    ]); ?>
                    <?= $form->field($model, 'title')->textInput()->label('Заголовок'); ?>
                    <?= $form->field($model, 'text_short')->textInput()->label('Короткое описание (вступительный текст, превью цитаты)'); ?>
                    <?= $form->field($model, 'text')->textarea(['rows' => '6', 'class' => 'gre'])->label('Текст статьи'); ?>
                    <?php $items = ArrayHelper::map(Item::find()->all(), 'id', 'name'); ?>
                    <?= $form->field($model, 'item_id')->dropDownList($items)->label('Источник'); ?>
                    <?//= $form->field($model, 'item_type_id')->textInput(['type' => 'hidden', 'value' => $type == 'author' ? '1' : '2'])->label(false); ?>
                    <div class="form-group">
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>