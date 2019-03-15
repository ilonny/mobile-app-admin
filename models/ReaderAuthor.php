<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reader_author".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $name_eng
 *
 * @property ReaderBook[] $readerBooks
 */
class ReaderAuthor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reader_author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['name', 'name_eng'], 'string', 'max' => 255],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReaderBooks()
    {
        return $this->hasMany(ReaderBook::className(), ['reader_author_id' => 'id']);
    }
}
