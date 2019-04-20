<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "audio_author".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $name_eng
 * @property string $name_es
 *
 * @property AudioBook[] $audioBooks
 */
class AudioAuthor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audio_author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['name', 'name_eng', 'name_es'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'name_eng' => 'Name Eng',
            'name_es' => 'Name Es',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAudioBooks()
    {
        return $this->hasMany(AudioBook::className(), ['audio_author_id' => 'id']);
    }
}
