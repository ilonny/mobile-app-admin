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
            [['name', 'file_src'], 'required'],
            [['name', 'description', 'file_src', 'other'], 'string'],
            [['reader_author_id'], 'integer'],
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
