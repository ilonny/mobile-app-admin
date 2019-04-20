<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "toc".
 *
 * @property int $id
 * @property string $app_href
 * @property string $title
 * @property int $book_id
 * @property string $other
 */
class Toc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'toc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['app_href', 'title', 'book_id'], 'required'],
            [['book_id'], 'integer'],
            [['other'], 'string'],
            [['app_href', 'title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'app_href' => 'App Href',
            'title' => 'Title',
            'book_id' => 'Book ID',
            'other' => 'Other',
        ];
    }
}
