<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;
    use app\models\ReaderBook;
    use yii\helpers\ArrayHelper;
    use app\models\ReaderAuthor;
?>
<div class="body-content">
    <div class="row">
        <h1>Список книг</h1>
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
                                            <?= $model->name_es ? '<br>('.$model->name_es.')' : ''; ?>
                                        </td>
                                        <td>
                                            <?= $model->description; ?>
                                            <?= $model->description_eng ? '<br>('.$model->description_eng.')' : ''; ?>
                                            <?= $model->description_es ? '<br>('.$model->description_es.')' : ''; ?>
                                        </td>
                                        <td>
                                            <?= $model->readerAuthor->name; ?>
                                            <?= $model->readerAuthor->name_eng; ?>
                                            <?= $model->readerAuthor->name_es; ?>
                                        </td>
                                        <td>
                                            <?= Html::beginForm(['/reader/delete', 'id' => $model->id], 'post'); ?>
                                            <?= Html::submitButton(
                                                    'удалить',
                                                    ['class' => 'btn btn-danger pull-right']
                                            )?>
                                            <?= Html::endForm(); ?>
                                            <a href="<?= Url::to(['reader/edit', 'id' => $model->id]) ?>" class="btn btn-primary pull-right">Редактировать</a>
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
                        $model = new ReaderBook;
                    ?>
                    <?= $form->field($model, 'name')->textInput()->label('Наименование'); ?>
                    <?= $form->field($model, 'name_eng')->textInput()->label('Наименование на английском'); ?>
                    <?= $form->field($model, 'name_es')->textInput()->label('Наименование на испанском'); ?>
                    <?//= $form->field($model, 'item_type_id')->textInput(['type' => 'hidden', 'value' => $type == 'author' ? '1' : '2'])->label(false); ?>
                    <?= $form->field($model, 'description')->textInput()->label('Короткое описание (не обязательно)'); ?>
                    <?= $form->field($model, 'description_eng')->textInput()->label('Короткое описание на английском (не обязательно)'); ?>
                    <?= $form->field($model, 'description_es')->textInput()->label('Короткое описание на испанском (не обязательно)'); ?>
                    <?php $reader_authors = ArrayHelper::map(ReaderAuthor::find()->all(), 'id', 'name'); ?>
                    <?= $form->field($model, 'reader_author_id')->dropDownList($reader_authors)->label('Автор'); ?>
                    <?= $form->field($uploadModel, 'file')->fileInput()->label('Загрузить книгу'); ?>
                    <?= $form->field($uploadModel, 'file_eng')->fileInput()->label("Загрузить книгу английская версия (загруженный файл {$model->file_src_eng})"); ?>
                    <?= $form->field($uploadModel, 'file_es')->fileInput()->label("Загрузить книгу английская версия (загруженный файл {$model->file_src_es})"); ?>
                    <div class="form-group">
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>