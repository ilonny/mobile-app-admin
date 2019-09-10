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
                                <th>Текст на английском</th>
                                <th>Текст на испанском</th>
                                <th>Город</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pushes as $push): ?>
                                <tr>
                                    <td><?= $push->id; ?></td>
                                    <td><?= $push->payload; ?></td>
                                    <td><?= $push->payload_eng; ?></td>
                                    <td><?= $push->payload_es; ?></td>
                                    <td><?= $push->other; ?></td>
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
                    <?= $form->field($model, 'payload')->textInput()->label('Текст уведомления'); ?>
                    <?= $form->field($model, 'payload_eng')->textInput()->label('Текст уведомления для английской версии'); ?>
                    <?= $form->field($model, 'payload_es')->textInput()->label('Текст уведомления для испанской версии'); ?>
                    <?php if (Yii::$app->user->identity->username == 'admin'): ?>
                        <?
                            $items = [
                                'admin' => 'Все',
                                'moscow' => 'Москва',
                                'saint_petersburg' => 'Санкт-Петербург',
                                'sukhum' => 'Сухум',
                                'sochi' => 'Сочи',
                                'khabarovsk' => 'Хабаровск',
                                'tomsk' => 'Томск',
                                'nizhny_novgorod' => 'Нижний Новгород',
                                'novgorod_the_great' => 'Великий Новгород',
                                'orsk' => 'Орск',
                                'izhevsk' => 'Ижевск',
                                'kyiv' => 'Киев',
                                'kharkiv' => 'Харьков',
                                'khokhlovka' => 'Хохловка',
                            ];
                            $params = [
                                'options' => [
                                    '1' => [
                                        'Selected' => true,
                                        // 'data-test' => 100,
                                    ],
                                    '2' => [
                                        // 'data-test' => 107,
                                    ],
                                ],
                            ];
                            echo $form->field($model, 'other', [
                                'inputOptions' => ['class' => 'form-control'],
                            ])->dropDownList($items, $params)->label("Город");
                        ?>
                    <?php else: ?>
                        <?= $form->field($model, 'other')->hiddenInput(['value' => Yii::$app->user->identity->username])->label(''); ?>
                    <?php endif; ?>
                    <div class="form-group">
                        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>