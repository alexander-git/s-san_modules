<?php

namespace app\modules\personal\controllers;

use Yii;

use yii\web\Controller;
use yii\helpers\ArrayHelper;
use app\modules\personal\models\PersonalApi;
use app\modules\personal\models\SettingsPost;

class DefaultController extends Controller
{
    protected function getDepartmentsList()
    {
        return PersonalApi::getDepartmentsList();
    }
    
    protected function getDepartmentName($departmentId)
    {
        return PersonalApi::getDepartmentName($departmentId);
    }
    
    protected function getSettingsPostsList()
    {
        $settingsPosts = SettingsPost::find()->all();
        return ArrayHelper::map($settingsPosts, 'id', 'name');
    }   
    
    protected function getSettingsPostsListWithEmptyItem()
    {
        $settingsPosts = SettingsPost::find()->all();
        
        return ArrayHelper::merge(
            [null => ''],
            ArrayHelper::map($settingsPosts, 'id', 'name')
        );
    } 
    
    protected function getUserId()
    {
        return PersonalApi::getCurrentUserId();
    }
    
    protected function getHaveList()
    {
        return [
            0 => 'Нет',
            1 => 'Есть'
        ];
    }
    
    protected function getYesNoList()
    {
        return [
            0 => 'Нет',
            1 => 'Да'
        ];
    }
    
    protected function getDocumentsPath()
    {
        return Yii::getAlias($this->module->documentsPath);
    }
    
    protected function getDocumentsUrl()
    {
        return Yii::getAlias($this->module->documentsUrl);
    }
    
}
