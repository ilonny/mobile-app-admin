<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quote".
 *
 * @property int $id
 * @property string $title
 * @property string $text_short
 * @property string $text
 * @property int $item_id
 * @property int $date
 * @property string $img_src
 * @property string $title_eng
 * @property string $text_short_eng
 * @property string $text_eng
 * @property string $title_es
 * @property string $text_short_es
 * @property string $text_es
 *
 * @property Item $item
 */
class Quote extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text_short', 'text', 'text_eng', 'text_es'], 'string'],
            [['item_id', 'date'], 'integer'],
            [['title', 'img_src', 'title_eng', 'text_short_eng', 'title_es', 'text_short_es'], 'string', 'max' => 255],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'text_short' => 'Text Short',
            'text' => 'Text',
            'item_id' => 'Item ID',
            'date' => 'Date',
            'img_src' => 'Img Src',
            'title_eng' => 'Title Eng',
            'text_short_eng' => 'Text Short Eng',
            'text_eng' => 'Text Eng',
            'title_es' => 'Title Es',
            'text_short_es' => 'Text Short Es',
            'text_es' => 'Text Es',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    //id - id цитаты, по ней ищем айтем и возвращаем его имя (автор или книга)
    public function getAuthorType($id)
    {
        $item = Item::find()->andWhere(['id' => $this->item_id])->one();
        if ($item->item_type_id == 1){
            return 'Автор';
        } else {
            return 'Книга';
        }
    }

    public function getAuthorName($id)
    {
        $item = Item::find()->andWhere(['id' => $this->item_id])->one();
        if ($item->name){
            return $item->name;
        } else {
            return 'Не указано';
        }
    }

    public function getAuthorNameEng($id)
    {
        $item = Item::find()->andWhere(['id' => $this->item_id])->one();
        if ($item->name_eng){
            return $item->name_eng;
        } else {
            return $item->name;
        }
    }
    public function getAuthorNameEs($id)
    {
        $item = Item::find()->andWhere(['id' => $this->item_id])->one();
        if ($item->name_es){
            return $item->name_es;
        } else {
            return $item->name;
        }
    }
}
