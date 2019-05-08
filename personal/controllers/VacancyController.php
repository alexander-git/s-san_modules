<?php

namespace app\modules\personal\controllers;

use Yii;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\personal\models\Vacancy;
use app\modules\personal\models\search\VacancySearch;
use app\modules\personal\models\PersonalApi;

class VacancyController extends DefaultController
{
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
        $searchModel = new VacancySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'departmentsList' => $this->getDepartmentsList(),
            'settingsPostsList' => $this->getSettingsPostsList(),
            'usersList' => $this->getUsersList(),
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Vacancy();

        if ($model->load(Yii::$app->request->post())) {
            $success = Vacancy::createVacancy($model, $this->getUserId());
            if ($success) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        
        return $this->render('create', [
            'model' => $model,
            'departmentsList' => $this->getDepartmentsList(),
            'settingsPostsList' => $this->getSettingsPostsList(),
        ]);
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Vacancy::SCENARIO_UPDATE;
        
        if ($model->load(Yii::$app->request->post())) {
            $success = Vacancy::updateVacancy($model, $this->getUserId());
            if ($success) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        
        return $this->render('update', [
            'model' => $model,
            'departmentsList' => $this->getDepartmentsList(),
            'settingsPostsList' => $this->getSettingsPostsList(),
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    private function findModel($id)
    {
        $model = Vacancy::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } 
        
        return $model;
    }
   
    private function getUsersList()
    {
        return PersonalApi::getUsersListForVacancy();
    }
     
}
