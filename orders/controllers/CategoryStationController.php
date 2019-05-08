<?php

namespace app\modules\orders\controllers;

use Yii;

use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\base\Model;

use app\modules\orders\models\Station;
use app\modules\orders\models\CategoryStation;
use app\modules\orders\models\OrdersApi;

class CategoryStationController extends DefaultController
{
    private $cityNamesCache = [];
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    
    public function actionIndex()
    {
        return $this->render('index', [
            'citiesList' => $this->getCitiesList(),
        ]);
    }
    
    public function actionView($cityId)
    {
        $models = CategoryStation::find()
            ->where(['city_id' => $cityId])
            ->indexBy('category_id')
            ->all();
        
        return $this->render('view', [
            'models' => $models,
            'cityId' => $cityId,
            'cityName' => $this->getCityNameById($cityId),
            'stationsList' => $this->getStationsList(),
            'categoriesList' => $this->getCategoriesList($cityId),
        ]);
        
    }
    
    public function actionUpdate($cityId)
    {
        $categoriesList = $this->getCategoriesList($cityId); 
        $categoriesIds = array_keys($categoriesList);
        
        CategoryStation::deleteAll([
                'and',
                ['not in', 'category_id', $categoriesIds],
                ['=', 'city_id', $cityId],
        ]);
        
        $categoryStations = CategoryStation::find()
            ->where(['city_id' => $cityId])
            ->indexBy('category_id')
            ->all();
        $isUpdate = count($categoryStations) > 0;
        
        $models = [];
        foreach ($categoriesIds as $categoryId) {
            if (isset($categoryStations[$categoryId])) {
                $models[$categoryId] = $categoryStations[$categoryId];
            } else {
                $model = new CategoryStation();
                $model->city_id = (int) $cityId;
                $model->category_id = $categoryId;
                $models[$categoryId] = $model; 
            }
        }
        
        if (
            Model::loadMultiple($models, Yii::$app->request->post()) &&
            Model::validateMultiple($models)
        ) {
            foreach ($models as $model) {
                $model->save(false);
            }
            return $this->redirect(['view', 'cityId' => $cityId]);
        }
        
        return $this->render('update', [
            'models' => $models,
            'isUpdate ' => $isUpdate ,
            'cityId' => $cityId,
            'cityName' => $this->getCityNameById($cityId),
            'stationsList' => $this->getStationsList(),
            'categoriesList' => $categoriesList,
        ]);
    }
    
    public function actionDelete($cityId)
    {
        CategoryStation::deleteAll(['=', 'city_id', $cityId]);
        return $this->redirect(['view', 'cityId' => $cityId]);
    }
    
    protected function getCityNameById($cityId)
    {
        if (isset($this->cityNamesCache[$cityId])) {
            return $this->cityNamesCache[$cityId];
        }
        
        $cityName = parent::getCityNameById($cityId);
        if ($cityName === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $this->cityNamesCache[$cityId] = $cityName;
        return $cityName;
    }
    
    private function getStationsList()
    {
        return Station::getStationsList();
    }
    
    private function getCategoriesList($cityId)
    {
        $cityName = $this->getCityNameById($cityId); 
        $categoriesList = OrdersApi::getCategoriesByCityName($cityName);
        return ArrayHelper::map($categoriesList, 'id', 'name'); 
    }

}
