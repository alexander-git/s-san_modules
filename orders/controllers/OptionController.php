<?php

namespace app\modules\orders\controllers;

use Yii;

use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use app\modules\orders\models\Option;
use app\modules\orders\models\OptionVal;
use app\modules\orders\models\search\OptionSearch;
use app\modules\orders\models\search\OptionValSearch;
use app\modules\orders\models\OrdersApi;

class OptionController extends DefaultController
{
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'optionDelete' => ['post'],
                    'valDelete' => ['post'],
                ],
            ],
        ];
    }
    
    public function actionIndex() 
    {
        $defaultCity = [
            OrdersApi::getDefaultCityId() => OrdersApi::getDefaultCityName(), 
        ];
        
        $citiesList = ArrayHelper::merge(
            $defaultCity, 
            $this->getCitiesList()
        );
        
        return $this->render('index', [
            'citiesList' => $citiesList,
        ]);
    }
    
    public function actionOptionIndex()
    {
        $searchModel = new OptionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('optionIndex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionOptionCreate()
    {
        $model = new Option();    
        if (
            $model->load(Yii::$app->request->post()) && 
            $model->save()
        ) {
            Yii::$app->session->setFlash('success', 'Создание успешно.');
            return $this->redirect(['option-index']);
        } 
        
        return $this->render('optionCreate', [
            'model' => $model,
        ]);
    }
        
    
    public function actionOptionUpdate($id)
    {
        $model = $this->findOptionModel($id);
       
        if (
            $model->load(Yii::$app->request->post()) && 
            $model->save()
        ) {
            Yii::$app->session->setFlash('success', 'Обновление успешно.');
            return $this->redirect(['option-index']);
        } 
        
        return $this->render('optionUpdate', [
            'model' => $model,
        ]);
    }
    
    public function actionOptionDelete($id)
    {
        $this->findOptionModel($id)->delete();
        return $this->redirect(['option-index']);
    }
    
    public function actionValIndex($cityId) 
    {
        $searchModel = new OptionValSearch();
        $dataProvider = $searchModel->search($cityId, Yii::$app->request->queryParams);
        
        return $this->render('valIndex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'cityId' => $cityId,
            'cityName' => $this->getCityNameById($cityId),
        ]);
    }
    
    public function actionValCreate($cityId)
    {
        $model = new OptionVal();
        $model->city_id = $cityId;
        if (
            $model->load(Yii::$app->request->post()) &&
            $model->save()
        ) {
            return $this->redirect(['val-index',  'cityId' => $cityId]);
        }
        
        return $this->render('valCreate', [
            'model' => $model,
            'cityId' => $cityId,
            'cityName' => $this->getCityNameById($cityId),
            'optionsList' => $this->getPossibleOptionsList($cityId),
        ]);
    }
    
    public function actionValUpdate($optionId, $cityId)
    {
        $model = $this->findOptionValModel($optionId, $cityId);
        if (
            $model->load(Yii::$app->request->post()) &&
            $model->save()
        ) {
            return $this->redirect(['val-index',  'cityId' => $cityId]);
        }
        
        return $this->render('valUpdate', [
            'model' => $model,
            'cityId' => $cityId,
            'cityName' => $this->getCityNameById($cityId),
        ]);
    }
    
    public function actionValDelete($optionId, $cityId)
    {
        $this->findOptionValModel($optionId, $cityId)->delete();
        return $this->redirect(['val-index', 'cityId' => $cityId]);
    }
   
    private function findOptionModel($id) 
    {
        $model = Option::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } 
        
        return $model;
    }
 
    private function findOptionValModel($optionId, $cityId) 
    {
        $model = OptionVal::findOne([
            'option_id' => $optionId, 
            'city_id' => $cityId
        ]);
        
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        return $model;
    }
    
    /**
     * Возвращает список настроек для определённого города, которые можно 
     * создать. Т.е. все настройки для города за исключением уже существующих.
     * @param integer $cityId
     * @return array 
     */
    private function getPossibleOptionsList($cityId) 
    {
        $options = Option::find()
            ->indexBy('id')
            ->all();
         
        $optionValsExist = OptionVal::find()
            ->select('option_id')
            ->where(['city_id' => $cityId])
            ->all();
        
        foreach ($optionValsExist as $optionVal) {
            unset($options[$optionVal->option_id]);
        }
         
        return ArrayHelper::map($options, 'id', 'name');
    }
    

}