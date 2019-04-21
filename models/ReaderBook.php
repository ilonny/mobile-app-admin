<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reader_book".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $reader_author_id
 * @property string $file_src
 * @property string $other
 * @property string $name_eng
 * @property string $description_eng
 * @property string $file_src_eng
 * @property string $name_es
 * @property string $description_es
 * @property string $file_src_es
 *
 * @property ReaderAuthor $readerAuthor
 * @property Toc[] $tocs
 */
class ReaderBook extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reader_book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'file_src', 'other', 'description_eng', 'file_src_eng', 'description_es', 'file_src_es'], 'string'],
            [['reader_author_id'], 'integer'],
            [['name_eng', 'name_es'], 'string', 'max' => 255],
            [['reader_author_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReaderAuthor::className(), 'targetAttribute' => ['reader_author_id' => 'id']],
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
            'reader_author_id' => 'Reader Author ID',
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
    public function getReaderAuthor()
    {
        return $this->hasOne(ReaderAuthor::className(), ['id' => 'reader_author_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTocs()
    {
        return $this->hasMany(Toc::className(), ['book_id' => 'id']);
    }
}
