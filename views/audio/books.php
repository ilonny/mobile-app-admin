<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    use app\models\AudioAuthor;
    use app\models\AudioBook;
?>
<div class="body-content">
    <div class="row">
        <h1>Список аудиокниг</h1>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#list">Список</a>
                </li>
                <li>
                    <a data-toggle="tab" href="#add">Добавить книгу</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="list" class="tab-pane fade in active list-group">
                    <?php if ($books): ?>
                        <table class="table table-striped table-bordered" id="data-table" style="margin: 30px 0;">
                            <thead>
                                <tr>
                                    <th>Заголовок</th>
                                    <th>Описание</th>
                                    <th>Автор</th>
                                    <th>Действие</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($books as $model): ?>
                                    <tr>
                                        <td>
                                            <?= $model->name; ?>
                                            <?= $model->name_eng ? '<br>('.$model->name_eng.')' : ''; ?>
                                        </td>
                                        <td>
                                            <?= $model->description; ?>
                                            <?= $model->description_eng ? '<br>('.$model->description_eng.')' : ''; ?>
                                        </td>
                                        <td>
                                            <?= $model->audioAuthor->name; ?>
                                            <?= $model->audioAuthor->name_eng; ?>
                                        </td>
                                        <td>
                                            <?= Html::beginForm(['/audio/delete', 'id' => $model->id], 'post'); ?>
                                            <?= Html::submitButton(
                                                    'удалить',
                                                    ['class' => 'btn btn-danger pull-right']
                                            )?>
                                            <?= Html::endForm(); ?>
                                            <a href="<?= Url::to(['audio/edit', 'id' => $model->id]) ?>" class="btn btn-primary pull-right">Редактировать</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <h2>Ничего не найдено</h2>
                    <?php endif; ?>
                </div>
                <div id="add" class="tab-pane">
                    <?php
                        $form = ActiveForm::begin();
                        $model = new AudioBook;
                    ?>
                    <?= $form->field($model, 'name')->textInput()->label('Наименование'); ?>
                    <?//= $form->field($model, 'item_type_id')->textInput(['type' => 'hidden', 'value' => $type == 'author' ? '1' : '2'])->label(false); ?>
                    <?= $form->field($model, 'description')->textInput()->label('Короткое описание (не обязательно)'); ?>
                    <?php $audio_authors = ArrayHelper::map(AudioAuthor::find()->all(), 'id', 'name');  ?>
                    <?= $form->field($model, 'audio_author_id')->dropDownList($audio_authors)->label('Автор'); ?>
                    <?//= $form->field($uploadModel, 'file')->fileInput()->label('Загрузить книгу'); ?>
                    <div class="form-group">
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>