<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "audiofile".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $file_src
 * @property int $audio_book_id
 * @property string $other
 * @property int $sort
 * @property int $reader_book_id
 * @property int $toc_id
 *
 * @property AudioBook $audioBook
 */
class Audiofile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audiofile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'file_src', 'audio_book_id'], 'required'],
            [['description', 'file_src', 'other'], 'string'],
            [['audio_book_id', 'sort', 'reader_book_id', 'toc_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['audio_book_id'], 'exist', 'skipOnError' => true, 'targetClass' => AudioBook::className(), 'targetAttribute' => ['audio_book_id' => 'id']],
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
            'file_src' => 'File Src',
            'audio_book_id' => 'Audio Book ID',
            'other' => 'Other',
            'sort' => 'Sort',
            'reader_book_id' => 'Reader Book ID',
            'toc_id' => 'Toc ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAudioBook()
    {
        return $this->hasOne(AudioBook::className(), ['id' => 'audio_book_id']);
    }
}
