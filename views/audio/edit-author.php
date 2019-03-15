<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;
    use app\models\AudioAuthor;
$form = ActiveForm::begin();
?>
<h1>Редактирование автора <?=$model->name?></h1>
<?= $form->field($model, 'name')->textInput()->label('Наименование'); ?>
<?= $form->field($model, 'name_eng')->textInput()->label('Наименование (на английском'); ?>
<?//= $form->field($model, 'item_type_id')->textInput(['type' => 'hidden', 'value' => $type == 'author' ? '1' : '2'])->label(false); ?>
<?= $form->field($model, 'description')->textInput()->label('Короткое описание (не обязательно)'); ?>
<div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>