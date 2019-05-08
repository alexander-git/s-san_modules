<?php

namespace app\modules\picker\controllers;

use Yii;

use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use app\modules\picker\models\PickerApi;
use app\modules\picker\models\Options;
use app\modules\picker\models\OptionsVal;
use app\modules\picker\models\search\OptionsSearch;
use app\modules\picker\models\search\OptionsValSearch;

class OptionsController extends DefaultController
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
        $defaultDepartment = [
            PickerApi::getDefaultDepartmentId() => PickerApi::getDefaultDepartmentName(), 
        ];
        
        $departmentsList = ArrayHelper::merge(
            $defaultDepartment, 
            PickerApi::getDepartmentsList()
        );
        
        return $this->render('index', [
            'departmentsList' => $departmentsList,
        ]);
    }
    
    public function actionOptionIndex()
    {
        $searchModel = new OptionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('optionIndex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionOptionCreate()
    {
        $model = new Options();    
        if (
            $model->load(Yii::$app->request->post()) && 
            $model->save()
        ) {
            Yii::$app->session->setFlash('success', 'Опция успешно создана');
            return $this->redirect(['option-update', 'id' => $model->id]);
        } else {
            return $this->render('optionCreate', [
                'model' => $model,
            ]);
        }
    }
        
    
    public function actionOptionUpdate($id)
    {
        $model = $this->findOptionsModel($id);
       
        if (
            $model->load(Yii::$app->request->post()) && 
            $model->save()
        ) {
            Yii::$app->session->setFlash('success', 'Опция успешно обновлена');
        } 
        
        return $this->render('optionUpdate', [
            'model' => $model,
        ]);
    }
    
    public function actionOptionDelete($id)
    {
        $this->findOptionsModel($id)->delete();
        return $this->redirect(['option-index']);
    }
    
    public function actionValIndex($departmentId) 
    {
        $searchModel = new OptionsValSearch($departmentId);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('valIndex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'departmentId' => $departmentId,
            'departmentName' => $this->getDepartmentName($departmentId),
        ]);
    }
    
    public function actionValCreate($departmentId)
    {
        $model = new OptionsVal();
        $model->depart_id = $departmentId;
        if (
            $model->load(Yii::$app->request->post()) &&
            $model->save()
        ) {
            return $this->redirect(['val-index', 'departmentId' => $departmentId]);
        }
        
        return $this->render('valCreate', [
            'model' => $model,
            'departmentId' => $departmentId,
            'departmentName' => $this->getDepartmentName($departmentId),
            'optionsList' => $this->getPossibleOptionsList($departmentId),
        ]);
    }
    
    public function actionValUpdate($optId, $departmentId)
    {
        $model = $this->findOptionsValModel($optId, $departmentId);
        if (
            $model->load(Yii::$app->request->post()) &&
            $model->save()
        ) {
            return $this->redirect(['val-index', 'departmentId' => $departmentId]);
        }
        
        return $this->render('valUpdate', [
            'model' => $model,
            'departmentId' => $departmentId,
            'departmentName' => $this->getDepartmentName($departmentId),
        ]);
    }
    
    public function actionValDelete($optId, $departmentId)
    {
        $this->findOptionsValModel($optId, $departmentId)->delete();
        return $this->redirect(['val-index', 'departmentId' => $departmentId]);
    }
   
    public function actionValuesDelete($departmentId)
    {
        OptionsVal::deleteByDepartmentId($departmentId);
        return $this->redirect(['index']);
    }
    
    private function findOptionsModel($id) 
    {
        $model = Options::findOne(['id' => $id]);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
 
    private function findOptionsValModel($optId, $departmentId) 
    {
        $model = OptionsVal::findOne([
            'opt_id' => $optId, 
            'depart_id' => $departmentId
        ]);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    private function getPossibleOptionsList($departmentId) 
    {
        $options = Options::find()
            ->indexBy('id')
            ->all();
         
        $optionsValExist = OptionsVal::find()
            ->select('opt_id')
            ->where(['depart_id' => $departmentId])
            ->all();
        
        foreach ($optionsValExist as $optionVal) {
            unset($options[$optionVal->opt_id]);
        }
         
        return ArrayHelper::map($options, 'id', 'label');
    }
    
    private function getDepartmentName($departmentId)
    {
        return PickerApi::getDepartmentName((int) $departmentId);
    }
    
}