<?php

namespace app\modules\personal\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\modules\personal\models\WorkTime;
use app\modules\personal\models\PersonalApi;

class ScheduleController extends DefaultController
{
    const DATE_FORMAT = 'd-m-Y';
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create-work-time' => ['post'],
                    'update-work-time' => ['post'],
                    'delete-work-time' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'departmentsList' => $this->getDepartmentsList(),
        ]);
    }

    public function actionSchedule($departmentId)
    {
        $get = Yii::$app->request->queryParams;
        
        if (isset($get['date'])) {
            $date = $get['date'];
        } else {
            $date = (new \DateTime())->format(self::DATE_FORMAT);
        }
        
        return $this->render('schedule', [
            'departmentId' => $departmentId,
            'departmentName' => $this->getDepartmentName($departmentId),
            'date' => $date,
        ]);
    }
    
    public function actionLoad($departmentId, $date) 
    {
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException();
        }
        
        $departmentId = (int) $departmentId;
        
        $pickers = $this->getPickersList($departmentId);
        $couriers = $this->getCouriersList($departmentId);
        
        $userIds = ArrayHelper::merge(
            array_keys($pickers),
            array_keys($couriers)
        );
        
        $result = new \stdClass();
        
        $pickersArr = [];
        foreach ($pickers as $id => $name) {
            $picker = new \stdClass();
            $picker->id = $id;
            $picker->name = $name;
            $pickersArr []= $picker;
        }
        
        $couriersArr = [];
        foreach ($couriers as $id => $name) {
            $courier = new \stdClass();
            $courier->id = $id;
            $courier->name = $name;
            $couriersArr []= $courier;
        }
        
        $pickersGroup = new \stdClass();
        $pickersGroup->name = 'Комплектовщики';
        $pickersGroup->users = $pickersArr;
        
        $couriersGroup = new \stdClass();
        $couriersGroup->name = 'Курьеры';
        $couriersGroup->users = $couriersArr;
        
        $result->groups = [];
        $result->groups [] = $pickersGroup;
        $result->groups [] = $couriersGroup;
        
        $result->workTimes = WorkTime::getWorkTimes($date, $userIds);
                
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
    
    public function actionCreateWorkTime() 
    {
        $model = new WorkTime();
        
        $post = Yii::$app->request->post();
        
        $model->date = $post['date'];
        $model->user_id = $post['userId'];
        $model->from = $post['from'];
        $model->to = $post['to'];
        
       $result = new \stdClass();
       
        if ($model->save()) {
            $result->success = true;
            $result->userId = $model->user_id;
            $result->workTimes = WorkTime::getWorkTimes($post['date'], [$model->user_id]);
        } else {
            $result->errorMessage = array_values($model->getFirstErrors())[0];
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
    
    public function actionUpdateWorkTime($id) 
    {
        $model = $this->findWorkTimeModel($id);
        
        $post = Yii::$app->request->post();
        
        $model->from = $post['from'];
        $model->to = $post['to'];
        
        $date = $model->date;
        
        $result = new \stdClass();
       
        if ($model->save()) {
            $result->success = true;
            $result->userId = $model->user_id;
            $result->workTimes = WorkTime::getWorkTimes($date, [$model->user_id]);
        } else {
            $result->errorMessage = array_values($model->getFirstErrors())[0];
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
    
    public function actionDeleteWorkTime($id) 
    {
        $model = $this->findWorkTimeModel($id);
        $date = $model->date;
        $userId = $model->user_id;
        if (!$model->delete()) {
            throw new \Exception();
        }
        
        $result = new \stdClass();
        $result->userId = $userId;
        $result->workTimes = WorkTime::getWorkTimes($date, [$userId]);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
    
    private function getPickersList($departmentId)
    {
        return PersonalApi::getPickersListByDepartmentId($departmentId);
    }
    
    private function getCouriersList($departmentId)
    {
        return PersonalApi::getCouriersListByDepartmentId($departmentId);
    }
    
    
    private function findWorkTimeModel($id)
    {
        $model = WorkTime::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } 
        
        return $model;
    }
 
}
