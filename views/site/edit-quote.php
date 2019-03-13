<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;
    use yii\widgets\ActiveForm;
    use app\models\Item;
    use app\models\Quote;
    use app\models\ItemType;
    use kartik\file\FileInput;

?>
<div class="body-content">
    <div class="row">
        <h1>Редактирование <?= $model->title; ?></h1>
        <h2><?= $answer; ?></h2>
        <?php
            $form = ActiveForm::begin();
        ?>
        <?= $form->field($model, 'img_src')->widget(FileInput::classname(), [
            'language' => 'ru',
            'options' => ['accept' => 'image/*'],
            'pluginOptions' => [
                'initialPreview'=>[
                    '/uploads/'.$model->img_src,
                ],
                'initialPreviewAsData' => true,
            ]
        ]); ?>
        <?= $form->field($model, 'title')->textInput()->label('Заголовок'); ?>
        <?= $form->field($model, 'text_short')->textInput()->label('Короткое описание (вступительный текст, превью цитаты)'); ?>
        <?= $form->field($model, 'text')->textarea(['rows' => '6', 'class' => 'gre'])->label('Текст статьи'); ?>
        <?php $items = ArrayHelper::map(Item::find()->all(), 'id', 'name'); ?>
        <?= $form->field($model, 'item_id')->dropDownList($items); ?>        
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
