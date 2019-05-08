<?php

namespace app\modules\cashdesks\controllers;

use Yii;

use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use app\modules\cashdesks\filters\CashdesksPrepareFilter;
use app\modules\cashdesks\models\AccountableTransact;
use app\modules\cashdesks\models\CashdesksApi;
use app\modules\cashdesks\models\search\AccountableTransactPickerSearch;
use app\modules\cashdesks\models\search\AccountableTransactDebtSearch;
use app\modules\cashdesks\exceptions\CashdeskNotEnoughMoneyException;

class AccountableController extends DefaultController
{
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'transact-delete' => ['post'],
                ],
            ],
            'cashdesksWork' => [
                'class' => CashdesksPrepareFilter::className(),
            ],
        ];
    } 
    
    public function actionIndex() 
    {
        $searchModel = new AccountableTransactDebtSearch($this->getDepartmentId());
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'model' => $this->getAccountable(),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionAcctabCourierIssue()
    {
        $model = new AccountableTransact();
        $model->scenario = AccountableTransact::SCENARIO_ACCTAB_OPERATION_CREATE;
        
        if ($model->load(Yii::$app->request->post())) {
            try {
                $success = AccountableTransact::createCourierIssue(
                    $model,
                    $this->getUserId(),
                    $this->getAccountable()
                );   
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Выдача денег успешана.');
                    return $this->redirect('index');
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            }
        }
        
        return $this->render('acctabCourierIssue', [
            'model' => $model,
            'couriersList' => $this->getCouriersList(),
        ]);
    }

    public function actionAcctabPickupIssue()
    {
        $model = new AccountableTransact();
        $model->scenario = AccountableTransact::SCENARIO_ACCTAB_OPERATION_CREATE;
        
        if ($model->load(Yii::$app->request->post())) {
            try {
                $success = AccountableTransact::createPickupIssue(
                    $model,
                    $this->getUserId(),
                    $this->getAccountable()
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Выдача денег успешана.');
                    return $this->redirect('index');
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            }
        }
            
        return $this->render('acctabPickupIssue', [
            'model' => $model,
        ]);
    }
    
    public function actionAcctabCourierReturn() 
    {
        $model = new AccountableTransact();
        $model->scenario = AccountableTransact::SCENARIO_ACCTAB_OPERATION_CREATE;
        
        if ($model->load(Yii::$app->request->post())) {
            try {
                $success = AccountableTransact::createCourierReturn(
                    $model,
                    $this->getUserId(),
                    $this->getAccountable()
                );

                if ($success) {
                    Yii::$app->session->setFlash('success', 'Возврат денег успешен.');
                    return $this->redirect('index');
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            }
        }
        
        return $this->render('acctabCourierReturn', [
            'model' => $model,
            'couriersList' => $this->getCouriersList(),
        ]);
    }
    
    public function actionAcctabPickupReturn() 
    {
        $model = new AccountableTransact();
        $model->scenario = AccountableTransact::SCENARIO_ACCTAB_OPERATION_CREATE;
        
        if ($model->load(Yii::$app->request->post())) {
            try {
                $success = AccountableTransact::createPickupReturn(
                    $model,
                    $this->getUserId(),
                    $this->getAccountable()
                );   
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Возврат денег успешен.');
                    return $this->redirect('index');
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            }            
        }
        
        return $this->render('acctabPickupReturn', [
            'model' => $model,
        ]);
    }
    
    public function actionHistory()
    {
        $searchModel = new AccountableTransactPickerSearch(
            $this->getUserId(),
            $this->getDepartmentId()
        );
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'usersList' => $this->getCouriersList(),
        ]);
    }
    
    
    public function actionTransactView($id)
    {
        $model = $this->findAccountableTransactModel($id);
        
        if (!$this->canPickerAccountableTransactAction($model)) {
            throw new ForbiddenHttpException();
        }
        
        return $this->render('transactView', [ 
            'model' => $model,
        ]);
    }
    
    public function actionTransactUpdate($id)
    {
        $model = $this->findAccountableTransactModel($id);
        $model->setUpdateScenario();
        
        if (!$this->canPickerAccountableTransactAction($model)) {
            throw new ForbiddenHttpException();
        }
        if (!$model->isAcctab) {
            throw new ForbiddenHttpException();
        }
        
        if ($model->load(Yii::$app->request->post())) {
            try {
                $success = AccountableTransact::updateOperation($model);
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Обновление успешно'); 
                    return $this->redirect(['history']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            }
        }
        
        $renderParams = $this->getRenderParamsForTransactUpdate($model);
        return $this->render('transactUpdate', $renderParams);
    }
    
    public function actionTransactDelete($id)
    {
        $model = $this->findAccountableTransactModel($id);
        
        if (!$this->canPickerAccountableTransactAction($model)) {
            throw new ForbiddenHttpException();
        }
        
        if (!$model->isAcctab) {
            throw new ForbiddenHttpException();
        }
        
        try {
            if (AccountableTransact::revertOperation($model)) {
                Yii::$app->session->setFlash('success', 'Отмена операции успешна.');
                return $this->redirect(['history']);
            } else {
                Yii::$app->session->setFlash('error', 'Произошла ошибка');
                return $this->redirect(['history']);
            }
        }
        catch (CashdeskNotEnoughMoneyException $e) {
            $this->processNotEnoughMoneyException($e);
        }        
    }
    
    private function getRenderParamsForTransactUpdate($accountableTransactModel) {
        $renderParams = [
            'model' => $accountableTransactModel,
        ];
        
        if ($accountableTransactModel->isAcctab) {
            if ($accountableTransactModel->isAcctabCourier) {
                $renderParams['usersList'] = $this->getCouriersList();
            }
        }
        
        return $renderParams;
    }
    
    private function findAccountableTransactModel($id)
    {
        $model = AccountableTransact::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        return $model;
    }
    
    private function processNotEnoughMoneyException($e)
    {
        Yii::$app->session->setFlash('error', $e->getMessage());
    }
    
    private function getCouriersList()
    {
        return CashdesksApi::getCouriersListByDepartmentId($this->getDepartmentId());
    }
    
}
