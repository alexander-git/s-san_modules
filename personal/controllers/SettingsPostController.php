<?php

namespace app\modules\personal\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use app\modules\personal\models\SettingsPost;
use app\modules\personal\models\search\SettingsPostSearch;
use app\modules\personal\exceptions\CanNotBeDeletedException;


class SettingsPostController extends DefaultController
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
        $searchModel = new SettingsPostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new SettingsPost();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'id' => $model->id,
                    'message' => 'Создание успешно.',
                ];
            } 
        }
        
        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }
    
    public function actionCreateValidate()
    {
        $model = new SettingsPost();
         
        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }
    
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'id' => $model->id,
                    'message' => 'Обновление успешно.'
                ];
            }
        }
        
        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }
    
    public function actionUpdateValidate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }
        
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        return $this->renderAjax('view', [
            'model' => $model
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        try {
            $success = SettingsPost::deleteSettingsPost($model);
            if (!$success) {
                Yii::$app->session->setFlash('error', 'Произошла ошибка.');   
            }
        } 
        catch (CanNotBeDeletedException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        
        return $this->redirect(['index']);
        
        /*
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => true,
            'id' => $id,
            'message' => 'Удаление успешно.',
        ];
        */
    }

    private function findModel($id)
    {
        $model = SettingsPost::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } 
        
        return $model;
    }
    
}
