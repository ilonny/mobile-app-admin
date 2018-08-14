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
}
