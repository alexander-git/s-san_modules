<?php

namespace app\modules\orders\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\orders\models\Stage;
use app\modules\orders\models\search\StageSearch;
use app\modules\orders\exceptions\CanNotBeDeletedException;

class StageController extends DefaultController
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
        $searchModel = new StageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        $model = new Stage();

        if (
            $model->load(Yii::$app->request->post()) && 
            $model->save()
        ) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (
            $model->load(Yii::$app->request->post()) && 
            $model->save()
        ) {
            return $this->redirect(['view', 'id' => $model->id]);
        } 
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        try {
            Stage::deleteStage($this->findModel($id));
        }
        catch (CanNotBeDeletedException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        
        return $this->redirect(['index']);
    }

    private function findModel($id)
    {
        $model = Stage::findOne(['id' => $id]);
        if ($model === null) {
             throw new NotFoundHttpException('The requested page does not exist.');
        } 
        
        return $model;
    }
    
}
