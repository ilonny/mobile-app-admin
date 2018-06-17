<?php
namespace app\components;

use app\models\Settings;
use yii\filters\AccessControl;

class Controller extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public function beforeAction($action)
    {
        if (\Yii::$app->user->isGuest) {
            \Yii::$app->user->loginRequired();
            \Yii::$app->end();
            return parent::beforeAction($action);
        }
        if (!file_exists(\Yii::getAlias('@app'). '/' . 'config/database.db')) {
            copy(\Yii::getAlias('@app'). '/template_files/database.db', \Yii::getAlias('@app'). '/config/database.db');
        }
        if (!Settings::find()->where(['is_installed' => true])->exists()) {
            $this->redirect(['/install/main/index']);
            return false;
        }
        return parent::beforeAction($action);
    }

}