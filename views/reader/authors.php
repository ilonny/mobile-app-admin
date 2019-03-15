<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;
    use app\models\ReaderAuthor;
?>
<div class="body-content">
    <div class="row">
        <h1>Список авторов</h1>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#list">Список</a>
                </li>
                <li>
                    <a data-toggle="tab" href="#add">Добавить автора</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="list" class="tab-pane fade in active list-group">
                    <?php if ($authors): ?>
                        <?php foreach ($authors as $model): ?>
                            <div class="list-group-item clearfix">
                                <span>
                                    <?= $model->name; ?>
                                    <?= $model->name_eng ? '<br>('.$model->name_eng.')' : ''; ?>
                                </span>
                                <?= Html::beginForm(['/reader/delete-author', 'id' => $model->id], 'post'); ?>
                                <?= Html::submitButton(
                                        'удалить',
                                        ['class' => 'btn btn-danger pull-right']
                                )?>
                                <?= Html::endForm(); ?>
                                <a href="<?= Url::to(['reader/edit-author', 'id' => $model->id]) ?>" class="btn btn-primary pull-right">Редактировать</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <h2>Ничего не найдено</h2>
                    <?php endif; ?>
                </div>
                <div id="add" class="tab-pane">
                    <?php
                        $form = ActiveForm::begin();
                        $model = new ReaderAuthor;
                    ?>
                    <?= $form->field($model, 'name')->textInput()->label('Наименование'); ?>
                    <?= $form->field($model, 'name_eng')->textInput()->label('Наименование на английском'); ?>
                    <?= $form->field($model, 'item_type_id')->textInput(['type' => 'hidden', 'value' => $type == 'author' ? '1' : '2'])->label(false); ?>
                    <?= $form->field($model, 'description')->textInput()->label('Короткое описание (не обязательно)'); ?>
                    <div class="form-group">
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>