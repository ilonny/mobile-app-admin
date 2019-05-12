<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;
    use app\models\AudioBook;
    use app\models\AudioAuthor;
    use yii\helpers\ArrayHelper;
    $form = ActiveForm::begin();
    $this->registerJsFile('/js/audio.js?v=2', ['depends' => '\app\assets\AppAsset']);
?>
<h1>Редактирование аудиокниги id = <?=$model->id?></h1>
<?= $form->field($model, 'name')->textInput()->label('Наименование'); ?>
<?= $form->field($model, 'name_eng')->textInput()->label('Наименование на английском'); ?>
<?= $form->field($model, 'name_es')->textInput()->label('Наименование (на испанском)'); ?>
<?//= $form->field($model, 'item_type_id')->textInput(['type' => 'hidden', 'value' => $type == 'author' ? '1' : '2'])->label(false); ?>
<?= $form->field($model, 'description')->textInput()->label('Короткое описание (не обязательно)'); ?>
<?= $form->field($model, 'description_eng')->textInput()->label('Короткое описание на английском (не обязательно)'); ?>
<?= $form->field($model, 'description_es')->textInput()->label('Короткое описание на испанском (не обязательно)'); ?>
<?php $audio_authors = ArrayHelper::map(AudioAuthor::find()->all(), 'id', 'name'); ?>
<?= $form->field($model, 'audio_author_id')->dropDownList($audio_authors)->label('Автор'); ?>
<div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
<hr>
<h4>Аудиофайлы</h4>
<div class="audiofiles-container"><?=$model->id;?></div>
<button class="btn btn-primary" data-toggle="modal" data-target="#audio-modal" id="open-audio-modal-btn">Добавить аудиофайл</button>

<div class="modal fade" tabindex="-1" id="audio-modal">
    <!-- render audio-modal -->
</div>