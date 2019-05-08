<?php

namespace app\modules\orders\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\orders\models\PaymentType;
use app\modules\orders\models\search\PaymentTypeSearch;
use app\modules\orders\exceptions\CanNotBeDeletedException;

class PaymentTypeController extends Controller
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
        $searchModel = new PaymentTypeSearch();
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
        $model = new PaymentType();

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
            PaymentType::deletePaymentType($this->findModel($id));
        }
        catch (CanNotBeDeletedException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        
        return $this->redirect(['index']);
        
    }

    private function findModel($id)
    {
        $model = PaymentType::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } 
        
        return $model;
    }
}
