<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;
    use yii\widgets\ActiveForm;
    use app\models\Item;
    use app\models\ItemType;
?>
<div class="body-content">
    <div class="row">
        <h1>Редактирование <?= $model->name; ?></h1>
        <h2><?= $answer; ?></h2>
        <?php
            $form = ActiveForm::begin();
        ?>
        <?= $form->field($model, 'name')->textInput()->label('Наименование'); ?>
        <?php
            $items = ArrayHelper::map(ItemType::find()->all(), 'id', 'name');
        ?>
        <?= $form->field($model, 'item_type_id')->dropDownList($items); ?>
        <?= $form->field($model, 'description')->textInput()->label('Короткое описание (не обязательно)'); ?>
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
