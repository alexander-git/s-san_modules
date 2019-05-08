<?php

namespace app\modules\cashdesks\controllers;

use Yii;

use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use app\modules\cashdesks\filters\UserFilter;
use app\modules\cashdesks\models\AdmincashTransact;
use app\modules\cashdesks\models\ExpenseType;
use app\modules\cashdesks\models\CashdesksApi;
use app\modules\cashdesks\models\search\AdmincashTransactBuhgalterSearch;
use app\modules\cashdesks\exceptions\CashdeskNotEnoughMoneyException;

class BuhgalterController extends DefaultController
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
            'userFilter' => [
                'class' => UserFilter::className(),
            ],
        ];
    } 
    
    public function actionIndex() 
    {
        $searchModel = new AdmincashTransactBuhgalterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'createdTransfersFromAdmincashCount' => $this->getCreatedTransfersFromAdmincashCount(),
            'departmentsList' => $this->getDepartmentsList(),
            'administratorsList' => $this->getAdministratorsList(),
            'usersEditList' => $this->getUsersEditList(),
        ]);
    }
    
    public function actionTransferView($id)
    {
        $model = $this->findAdmincashTransactModel($id);
        
        if (!$this->canBuhgalterAdmincashTransactAction($model)) {
            throw new ForbiddenHttpException;
        }
    
        return $this->render('transferView', [
            'model' => $model,
        ]);
    }
    
    public function actionTransferProcess($id)
    {
        $admincashTransactModel = $this->findAdmincashTransactModel($id);
        $banknotesModel = $admincashTransactModel->banknotes;
        
        $admincashTransactModel->scenario = AdmincashTransact::SCENARIO_EXPENSE_ACCDEP_OPERATION;
        $banknotesModel->scenario = Banknotes::SCENARIO_POSITIVE;
        
        if (!$this->canBuhgalterAdmincashTransactAction($admincashTransactModel)) {
            throw new ForbiddenHttpException;
        }
        if ($admincashTransactModel->isAccepted || $admincashTransactModel->isRejected) {
            throw new ForbiddenHttpException;  
        }
        
        $post = Yii::$app->request->post();

        $needLoad = isset($post['accept']) || isset($post['reject']);
        $accept = isset($post['accept']);
        $reject = isset($post['reject']);
        
        $loadSuccess = false;
        if ($needLoad) {
            $loadSuccess = 
                $admincashTransactModel->load($post) && 
                $banknotesModel->load($post);
        }
        
        try {
            if ($loadSuccess && $accept) {
                $success = AdmincashTransact::acceptAccdep(
                    $admincashTransactModel,
                    $this->getUserId(),
                    $banknotesModel
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Перевод принят успешно.');
                    return $this->redirect(['index']);
                }
            } elseif ($loadSuccess && $reject) {
                $success = AdmincashTransact::rejectAccdep(
                    $admincashTransactModel,
                    $this->getUserId()
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Перевод отклонён.');
                    return $this->redirect(['index']);
                }
            }
        }
        catch (CashdeskNotEnoughMoneyException $e) {
            $this->processNotEnoughMoneyException($e);
        }
        
        return $this->render('transferProcess', [
            'admincashTransactModel' => $admincashTransactModel,
            'banknotesModel' => $banknotesModel,
        ]);
    }

    public function actionTransferUpdate($id)
    {
        $admincashTransactModel = $this->findAdmincashTransactModel($id);
        $banknotesModel = $admincashTransactModel->banknotes;
        $admincashTransactModel->scenario = AdmincashTransact::SCENARIO_EXPENSE_ACCDEP_UPDATE;
        $banknotesModel->scenario = Banknotes::SCENARIO_POSITIVE;
        
        if (!$this->canBuhgalterAdmincashTransactAction($admincashTransactModel)) {
            throw new ForbiddenHttpException;
        }
        
        $post = Yii::$app->request->post();
        if (
            $admincashTransactModel->load($post) &&
            $banknotesModel->load($post)
        ) {
            try {
                $success = AdmincashTransact::updateOperation(
                    $admincashTransactModel,
                    $this->getUserId(),
                    $banknotesModel
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Обновление успешно.');
                    return $this->redirect(['index']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            }
        } 
        
        return $this->render('transferUpdate', [
            'admincashTransactModel' => $admincashTransactModel,
            'banknotesModel' => $banknotesModel,
        ]);
    }
    
    public function actionTransferDelete($id)
    {
        $admincashTransactModel = $this->findAdmincashTransactModel($id);
        
        if (!$this->canBuhgalterAdmincashTransactAction($admincashTransactModel)) {
            throw new ForbiddenHttpException;
        }
        
        try {
            if (AdmincashTransact::revertOperation($admincashTransactModel)) {
                Yii::$app->session->setFlash('success', 'Отмена операции успешна.');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Произошла ошибка');
                return $this->redirect(['index']);
            }
        }
        catch (CashdeskNotEnoughMoneyException $e) {
            $this->processNotEnoughMoneyException($e);
            return $this->redirect(['index']);
        }        
    }
    
    private function findAdmincashTransactModel($id)
    {
        $model = AdmincashTransact::find()
            ->with(['banknotes'])
            ->where(['id' => $id])
            ->one();
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        return $model;
    }
    
    private function getCreatedTransfersFromAdmincashCount()
    {
        return AdmincashTransact::find()
            ->where([
               'state' => AdmincashTransact::STATE_CREATED,
               'type' => AdmincashTransact::TYPE_EXPENSE,
               'type_id' => ExpenseType::getExpenseTypeAccdepId(),
            ])->count();
    }
    
    private function processNotEnoughMoneyException($e)
    {
        Yii::$app->session->setFlash('error', $e->getMessage());
    }
    
    private function getDepartmentsList()
    {
        return CashdesksApi::getDepartmentsList();
    }
    
    private function getAdministratorsList()
    {
        return CashdesksApi::getAdminstratorsList();
    }
    
    private function getUsersEditList()
    {
        return CashdesksApi::getUsersEditListForAdmincashTransact();
    }
    
}
