<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "audio_book".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $audio_author_id
 * @property string $file_src
 * @property string $other
 *
 * @property AudioAuthor $audioAuthor
 */
class AudioBook extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audio_book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'description', 'file_src', 'other'], 'string'],
            [['audio_author_id'], 'integer'],
            [['audio_author_id'], 'exist', 'skipOnError' => true, 'targetClass' => AudioAuthor::className(), 'targetAttribute' => ['audio_author_id' => 'id']],
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
            'audio_author_id' => 'Audio Author ID',
            'file_src' => 'File Src',
            'other' => 'Other',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAudioAuthor()
    {
        return $this->hasOne(AudioAuthor::className(), ['id' => 'audio_author_id']);
    }
}
