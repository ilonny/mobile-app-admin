<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;
    use app\models\ReaderBook;
    use yii\helpers\ArrayHelper;
    use app\models\ReaderAuthor;
$form = ActiveForm::begin();
$this->registerJsFile('/js/book-edit.js', ['depends' => '\app\assets\AppAsset']);
?>
<h1>Редактирование книги id = <?=$model->id?></h1>
<?= $form->field($model, 'name')->textInput()->label('Наименование'); ?>
<?//= $form->field($model, 'item_type_id')->textInput(['type' => 'hidden', 'value' => $type == 'author' ? '1' : '2'])->label(false); ?>
<?= $form->field($model, 'description')->textInput()->label('Короткое описание (не обязательно)'); ?>
<?php $reader_authors = ArrayHelper::map(ReaderAuthor::find()->all(), 'id', 'name'); ?>
<?= $form->field($model, 'reader_author_id')->dropDownList($reader_authors)->label('Автор'); ?>
<?= $form->field($uploadModel, 'file')->fileInput()->label("Загрузить книгу (загруженный файл {$model->file_src})"); ?>
<?php if ($model->tocs): ?>
    <hr>
    <h4>Главы книги (настройка связей с аудиофайлами, (для реализации перехода из ридера в аудио и обратно))</h4>
    <table class="table">
        <thead>
            <th>Глава</th>
            <th>Аудиокнига</th>
            <th>Глава аудиокниги</th>
        </thead>
        <?php foreach ($model->tocs as $toc): ?>
            <tr>
                <td>
                    <?= $toc->title; ?>
                </td>
                <td id="select-audio-book-<?=$toc->id?>">
                    <select name="audio_book_id" id="audio_book_id" data-toc-id="<?= $toc->id; ?>" class="form-control" style="display: inline-block; width: auto;">
                        <option <?= (!$toc->audio_book_id) ? "selected" : ""; ?> value="0">Не указано</option>
                        <?php foreach ($audioBooks as $audioBook): ?>
                            <option <?= ($toc->audio_book_id == $audioBook->id) ? "selected" : ""; ?> value="<?= $audioBook->id; ?>"><?= $audioBook->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td class="audiofile_select_ajax_render"></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>