<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "push".
 *
 * @property int $id
 * @property string $payload
 * @property string $payload_eng
 * @property string $payload_es
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
            [['payload', 'payload_eng', 'payload_es', 'other'], 'string'],
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
            'payload_eng' => 'Payload Eng',
            'payload_es' => 'Payload Es',
            'other' => 'Other',
        ];
    }
}
