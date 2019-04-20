<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "push".
 *
 * @property int $id
 * @property string $payload
 * @property string $other
 */
class Push extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'push';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payload'], 'required'],
            [['other'], 'string'],
            [['payload'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'payload' => 'Payload',
            'other' => 'Other',
        ];
    }
}
