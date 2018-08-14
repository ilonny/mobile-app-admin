<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;
    use app\models\ReaderBook;
    use yii\helpers\ArrayHelper;
    use app\models\ReaderAuthor;
$form = ActiveForm::begin();
?>
<h1>Редактирование книги id = <?=$model->id?></h1>
<?= $form->field($model, 'name')->textInput()->label('Наименование'); ?>
<?//= $form->field($model, 'item_type_id')->textInput(['type' => 'hidden', 'value' => $type == 'author' ? '1' : '2'])->label(false); ?>
<?= $form->field($model, 'description')->textInput()->label('Короткое описание (не обязательно)'); ?>
<?php $reader_authors = ArrayHelper::map(ReaderAuthor::find()->all(), 'id', 'name'); ?>
<?= $form->field($model, 'reader_author_id')->dropDownList($reader_authors)->label('Автор'); ?>
<?= $form->field($uploadModel, 'file')->fileInput()->label("Загрузить книгу (загруженный файл {$model->file_src})"); ?>
<div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>