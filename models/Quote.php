<?php

namespace app\models;

use Yii;
use app\models\Item;
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
            [['title'], 'required'],
            [['text_short', 'text'], 'string'],
            [['item_id', 'date'], 'integer'],
            [['title', 'img_src'], 'string', 'max' => 255],
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
        // if ($item->item_type_id == 1){
        // } else {
        //     return $item->title;
        // }
    }
}
