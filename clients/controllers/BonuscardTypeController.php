<?php

namespace app\modules\clients\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\modules\clients\controllers\DefaultController;
use app\modules\clients\models\BonuscardType;
use app\modules\clients\models\search\BonuscardTypeSearch;


class BonuscardTypeController extends DefaultController
{
    /**
     * @inheritdoc
     */
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
        $searchModel = new BonuscardTypeSearch();
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
        $model = new BonuscardType();

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
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        $model = BonuscardType::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } 
        
        return $model;
    }
    
}
