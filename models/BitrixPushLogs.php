<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bitrix_push_logs".
 *
 * @property int $id
 * @property string $type
 * @property int $site_id
 * @property string $other
 * @property string $date_create
 * @property string $news_title
 */
class BitrixPushLogs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bitrix_push_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'site_id'], 'required'],
            [['site_id'], 'integer'],
            [['other', 'news_title'], 'string'],
            [['date_create'], 'safe'],
            [['type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'site_id' => 'Site ID',
            'other' => 'Other',
            'date_create' => 'Date Create',
            'news_title' => 'News Title',
        ];
    }
}
