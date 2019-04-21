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
 * @property string $name_eng
 * @property string $description_eng
 * @property string $file_src_eng
 * @property string $name_es
 * @property string $description_es
 * @property string $file_src_es
 *
 * @property AudioAuthor $audioAuthor
 * @property Audiofile[] $audiofiles
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
            [['name', 'description', 'file_src', 'other', 'name_eng', 'description_eng', 'file_src_eng', 'name_es', 'description_es', 'file_src_es'], 'string'],
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
            'name_eng' => 'Name Eng',
            'description_eng' => 'Description Eng',
            'file_src_eng' => 'File Src Eng',
            'name_es' => 'Name Es',
            'description_es' => 'Description Es',
            'file_src_es' => 'File Src Es',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAudioAuthor()
    {
        return $this->hasOne(AudioAuthor::className(), ['id' => 'audio_author_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAudiofiles()
    {
        return $this->hasMany(Audiofile::className(), ['audio_book_id' => 'id']);
    }
}
