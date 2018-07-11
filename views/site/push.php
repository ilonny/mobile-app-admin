<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;
    use yii\widgets\ActiveForm;
    use app\models\Quote;
    use app\models\Item;
    use app\models\Push;
?>
<div class="body-content">
    <div class="row">
        <h1>Push уведомления</h1>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#list">Список push-уведомлений</a>
                </li>
                <li>
                    <a data-toggle="tab" href="#add">Отправить push-уведомление</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="list" class="tab-pane active fade in  list-group" style="padding:30px 0;">
                    <table class="table table-striped table-bordered" id="data-table" style="margin: 30px 0;">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Текст</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pushes as $push): ?>
                                <tr>
                                    <td><?= $push->id; ?></td>
                                    <td><?= $push->payload; ?></td>                                    
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div id="add" class="tab-pane ">
                    <?php
                        $form = ActiveForm::begin();
                        $model = new Push;
                    ?>
                    <?= $form->field($model, 'payload')->textInput()->label('Текст уведомления (желательно не больше 200 символов'); ?>
                    <div class="form-group">
                        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>