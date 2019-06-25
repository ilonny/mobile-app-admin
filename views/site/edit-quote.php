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
        <?= $form->field($model, 'title_eng')->textInput()->label('Заголовок (на английском)'); ?>
        <?= $form->field($model, 'title_es')->textInput()->label('Заголовок (на испанском)'); ?>
        <?= $form->field($model, 'text_short')->textInput()->label('Короткое описание (вступительный текст, превью цитаты)'); ?>
        <?= $form->field($model, 'text_short_eng')->textInput()->label('Короткое описание на английском (вступительный текст, превью цитаты)'); ?>
        <?= $form->field($model, 'text_short_es')->textInput()->label('Короткое описание на испанском (вступительный текст, превью цитаты)'); ?>
        <?= $form->field($model, 'text')->textarea(['rows' => '6', 'class' => 'gre'])->label('Текст статьи'); ?>
        <?= $form->field($model, 'text_eng')->textarea(['rows' => '6', 'class' => 'gre'])->label('Текст статьи (на английском)'); ?>
        <?= $form->field($model, 'text_es')->textarea(['rows' => '6', 'class' => 'gre'])->label('Текст статьи (на испанском)'); ?>
        <?php 
            $items = Item::find()->all();
            foreach ($items as $key_item => $item) {
                $items[$key_item]->name = $items[$key_item]->name.($items[$key_item]->name_eng ? ' (eng: '.$items[$key_item]->name_eng.')' : '').($items[$key_item]->name_es ? ' (es: '.$items[$key_item]->name_es.')' : '');
            }
            $items = ArrayHelper::map($items, 'id', 'name');
        ?>
        <?= $form->field($model, 'item_id')->dropDownList($items); ?>        
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
