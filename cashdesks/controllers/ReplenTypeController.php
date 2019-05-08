<?php

namespace app\modules\cashdesks\controllers;

use Yii;

use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use app\modules\cashdesks\models\ReplenType;
use app\modules\cashdesks\models\search\ReplenTypeSearch;

class ReplenTypeController extends Controller
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
        $searchModel = new ReplenTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new ReplenType();

        if (
            $model->load(Yii::$app->request->post()) && 
            $model->save()
        ) {
            Yii::$app->session->setFlash('success', 'Создание успешно.');
            return $this->redirect('index');
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
            Yii::$app->session->setFlash('success', 'Обновление успешно.');
            return $this->redirect(['update', 'id' => $model->id]);
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        $model = ReplenType::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } 
        
        return $model;
    }
    
}
