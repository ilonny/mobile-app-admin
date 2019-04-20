<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "item".
 *
 * @property int $id
 * @property string $name
 * @property int $item_type_id
 * @property string $description
 * @property string $name_eng
 * @property string $description_eng
 * @property string $name_es
 * @property string $description_es
 *
 * @property ItemType $itemType
 * @property Quote[] $quotes
 */
class Item extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_type_id'], 'integer'],
            [['description', 'description_eng', 'description_es'], 'string'],
            [['name', 'name_eng', 'name_es'], 'string', 'max' => 255],
            [['item_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemType::className(), 'targetAttribute' => ['item_type_id' => 'id']],
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
            'item_type_id' => 'Item Type ID',
            'description' => 'Description',
            'name_eng' => 'Name Eng',
            'description_eng' => 'Description Eng',
            'name_es' => 'Name Es',
            'description_es' => 'Description Es',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemType()
    {
        return $this->hasOne(ItemType::className(), ['id' => 'item_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuotes()
    {
        return $this->hasMany(Quote::className(), ['item_id' => 'id']);
    }
}
