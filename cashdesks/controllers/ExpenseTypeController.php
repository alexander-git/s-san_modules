<?php

namespace app\modules\cashdesks\controllers;

use Yii;

use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use app\modules\cashdesks\models\ExpenseType;
use app\modules\cashdesks\models\ExpenseTypeItem;
use app\modules\cashdesks\models\search\ExpenseTypeSearch;
use app\modules\cashdesks\models\search\ExpenseTypeItemSearch;

class ExpenseTypeController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'type-delete' => ['post'],
                    'item-delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new ExpenseTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionTypeCreate() 
    {
        $model = new ExpenseType();
        
        if (
            $model->load(Yii::$app->request->post()) &&
            $model->save()
        ) {
            Yii::$app->session->setFlash('success', 'Вид расхода успшено создан.');
            return $this->redirect(['type-update', 'id' => $model->id]);
        }
        
        return $this->render('typeCreate', [
            'model' => $model,
        ]);
    }
    
    public function actionTypeUpdate($id) 
    {
        $model = $this->findExpenseTypeModel($id);
        $model->scenario = ExpenseType::SCENARIO_UPDATE;
        
        if (
            $model->load(Yii::$app->request->post()) &&
            $model->save()
        ) {
            Yii::$app->session->setFlash('success', 'Вид расхода успшено обновлён.');
        }
        
        $renderParams = [];
        $renderParams['model'] = $model;
        
        if ($model->isTypeSupplier || $model->isTypeArray) {
            $searchModel = new ExpenseTypeItemSearch($id);
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $renderParams['searchModel'] = $searchModel;
            $renderParams['dataProvider'] = $dataProvider;
        }
        
        return $this->render('typeUpdate', $renderParams);
    }
    
    public function actionTypeDelete($id) 
    {
        $this->findExpenseTypeModel($id)->delete();
        return $this->redirect(['index']); 
    }
    
    public function actionItemCreate($expenseTypeId) 
    {
    
        $expenseTypeItemModel = new ExpenseTypeItem();
    
        if ($expenseTypeItemModel->load(Yii::$app->request->post())) {
            $expenseTypeItemModel->expense_type_id = $expenseTypeId;
            if ($expenseTypeItemModel->save()) {
                return $this->redirect(['type-update', 'id' => $expenseTypeId]);
            }
        }
        
        return $this->render('itemCreate', [
            'expenseTypeModel' => $this->findExpenseTypeModel($expenseTypeId),
            'expenseTypeItemModel' => $expenseTypeItemModel,
        ]);
    }
    
    public function actionItemUpdate($expenseTypeId, $id) 
    {
       $expenseTypeItemModel= $this->findExpenseTypeItemModel($id);
        
        if (
            $expenseTypeItemModel->load(Yii::$app->request->post()) &&
            $expenseTypeItemModel->save()
        ) {
            return $this->redirect(['type-update', 'id' => $expenseTypeId]);
        }
        
        return $this->render('itemUpdate', [
            'expenseTypeModel' => $this->findExpenseTypeModel($expenseTypeId),
            'expenseTypeItemModel' => $expenseTypeItemModel,
        ]);
    }
    
    public function actionItemDelete($expenseTypeId, $id) 
    {
        $this->findExpenseTypeItemModel($id)->delete();
        return $this->redirect(['type-update', 'id' => $expenseTypeId]);
    }
    

    private function findExpenseTypeModel($id)
    {
        $model = ExpenseType::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } 
        
        return $model;
    }
    
    private function findExpenseTypeItemModel($id)
    {
        $model = ExpenseTypeItem::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        return $model;
    }
    
}
