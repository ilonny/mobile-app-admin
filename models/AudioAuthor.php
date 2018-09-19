<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "audio_author".
 *
 * @property int $id
 * @property string $name
 * @property string $description
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
            [['name'], 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
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