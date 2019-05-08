<?php

namespace app\modules\cashdesks\controllers;

use Yii;

use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use app\modules\cashdesks\filters\CashdesksPrepareFilter;
use app\modules\cashdesks\models\Banknotes;
use app\modules\cashdesks\models\PickercashTransact;
use app\modules\cashdesks\models\CashdesksApi;
use app\modules\cashdesks\models\search\PickercashTransactPickerSearch;
use app\modules\cashdesks\exceptions\CashdeskNotEnoughMoneyException;

class PickercashController extends DefaultController
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
        return $this->render('index', [
            'model' => $this->getPickercash(),
        ]);
    }
    
    public function actionReplenCourierCreate() 
    {
        $pickercashTransactModel = new PickercashTransact();
        $banknotesModel = new Banknotes();
        
        $pickercashTransactModel->scenario = PickercashTransact::SCENARIO_REPLEN_CREATE;
        $banknotesModel->scenario = Banknotes::SCENARIO_POSITIVE;
        
        $post = Yii::$app->request->post();
        if (
            $pickercashTransactModel->load($post) &&
            $banknotesModel->load($post)
        ) {
            try {
                $success = PickercashTransact::createReplenCourier(
                    $pickercashTransactModel, 
                    $this->getUserId(),
                    $this->getPickercash(),
                    $banknotesModel
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Пополнение успешно.');
                    return $this->redirect(['index']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            } 
        }
        
        return $this->render('replenCourierCreate', [
            'pickercashTransactModel' => $pickercashTransactModel,
            'banknotesModel' => $banknotesModel,
            'couriersList' => $this->getCouriersList(),
        ]);
    }
    
    public function actionReplenPickupCreate()
    {
        $pickercashTransactModel = new PickercashTransact();
        $banknotesModel = new Banknotes();
        
        $pickercashTransactModel->scenario = PickercashTransact::SCENARIO_REPLEN_CREATE;
        $banknotesModel->scenario = Banknotes::SCENARIO_POSITIVE;
        
        $post = Yii::$app->request->post();
        if (
            $pickercashTransactModel->load($post) &&
            $banknotesModel->load($post)
        ) {
            try {
                $success = PickercashTransact::createReplenPickup(
                    $pickercashTransactModel, 
                    $this->getUserId(),
                    $this->getPickercash(),
                    $banknotesModel
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Пополнение успешно.');
                    return $this->redirect(['index']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            }   
        }
        
        return $this->render('replenPickupCreate', [
            'pickercashTransactModel' => $pickercashTransactModel,
            'banknotesModel' => $banknotesModel
        ]);
    }
    
    public function actionTransferToAdmincash()
    {
        $pickercashTransactModel = new PickercashTransact();
        $banknotesModel = new Banknotes();
        
        $pickercashTransactModel->scenario = PickercashTransact::SCENARIO_TRANSFER_TO_ADMINCASH_CREATE;
        $banknotesModel->scenario = Banknotes::SCENARIO_POSITIVE;
        
        $post = Yii::$app->request->post();
        if (
            $pickercashTransactModel->load($post) &&
            $banknotesModel->load($post)
        ) {
            try {
                $success = PickercashTransact::createTransferToAdmincash(
                    $pickercashTransactModel, 
                    $this->getUserId(),
                    $this->getPickercash(),
                    $banknotesModel
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Перевод успешен.');
                    return $this->redirect(['index']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            } 
        }
        
        return $this->render('transferToAdmincash', [
            'pickercashTransactModel' => $pickercashTransactModel,
            'banknotesModel' => $banknotesModel
        ]);
    }

    public function actionExchange()
    {
        $pickercashTransactModel = new PickercashTransact();
        $banknotesModel = new Banknotes();
        
        $pickercashTransactModel->scenario = PickercashTransact::SCENARIO_CHANGE_PICKERCASH_CREATE;
        $banknotesModel->scenario = Banknotes::SCENARIO_EXCHANGE;
        
        $post = Yii::$app->request->post();
        if (
            $pickercashTransactModel->load($post) &&
            $banknotesModel->load($post)
        ) {
            try {
                $success = PickercashTransact::exchangePickercash(
                    $pickercashTransactModel, 
                    $this->getUserId(),
                    $this->getPickercash(),
                    $banknotesModel
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Размен успешен.');
                    return $this->redirect(['index']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            }             
        }
        
        return $this->render('exchange', [
            'pickercashTransactModel' => $pickercashTransactModel,
            'banknotesModel' => $banknotesModel
        ]);
    }
    
    public function actionHistory()
    {
        $departmentId = $this->getDepartmentId();
        
        $searchModel = new PickercashTransactPickerSearch(
            $this->getUserId(),
            $departmentId
        );
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'usersList' => $this->getUsersList(),
        ]);
    }
    
    
    public function actionTransactView($id)
    {
        $model = $this->findPickercashTransactModel($id);
        
        if (!$this->canPickerPickercashTransactAction($model)) {
            throw new ForbiddenHttpException();
        }
                
        return $this->render('transactView', [ 
            'model' => $model,
        ]);
    }
    
    public function actionTransactUpdate($id)
    {
        $pickercashTransactModel = $this->findPickercashTransactModel($id);
        $banknotesModel = $pickercashTransactModel->banknotes;
        
        if (!$this->canPickerPickercashTransactAction($pickercashTransactModel)) {
            throw new ForbiddenHttpException();
        }
        if (
            $pickercashTransactModel->isTypeTransferToAdmincash &&
            !$pickercashTransactModel->isCreated
        ) {
            throw new ForbiddenHttpException();
        }
        
        $pickercashTransactModel->setUpdateScenario(true);
        if ($pickercashTransactModel->isTypeExchange) {
            $banknotesModel->scenario = Banknotes::SCENARIO_EXCHANGE;
        } else {
            $banknotesModel->scenario = Banknotes::SCENARIO_POSITIVE; 
        }
        
        $post = Yii::$app->request->post();    
        if (
            $pickercashTransactModel->load($post) &&
            $banknotesModel->load($post)
        ) {
            try {
                $success = PickercashTransact::updateOperation(
                    $pickercashTransactModel,
                    $this->getUserId(),
                    $banknotesModel
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Обновление успешно');
                    return $this->redirect(['history']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            } 
        }
            
        return $this->render('transactUpdate', [
            'pickercashTransactModel' => $pickercashTransactModel,
            'banknotesModel' => $banknotesModel, 
        ]);
    }
    
    public function actionTransactDelete($id)
    {
        $pickercashTransactModel = $this->findPickercashTransactModel($id);
        
        if (!$this->canPickerPickercashTransactAction($pickercashTransactModel)) {
            throw new ForbiddenHttpException();
        }
        if (
            $pickercashTransactModel->isTypeTransferToAdmincash &&
            !$pickercashTransactModel->isCreated
        ) {
            throw new ForbiddenHttpException();
        }
        try {
            if (PickercashTransact::revertOperation($pickercashTransactModel)) {
                Yii::$app->session->setFlash('success', 'Отмена операции успешна.');
                return $this->redirect(['history']);
            } else {
                Yii::$app->session->setFlash('error', 'Произошла ошибка');
                return $this->redirect(['history']);
            }
        }
        catch (CashdeskNotEnoughMoneyException $e) {
            $this->processNotEnoughMoneyException($e);
            return $this->redirect(['history']);
        } 
    }
    
    public function getRenderParamsForTransactUpdate(
        $pickercashTransactModel, 
        $banknotesModel
    ) {
        $renderParams = [
            'pickercashTransactModel' => $pickercashTransactModel,
            'banknotesModel' => $banknotesModel,
        ];
        
        $needUsersList = false;
        $needBanknotes = true;
        $needBanknotesExchangeForm = false;
             
        if ($pickercashTransactModel->isTypeReplen) {
            if ($pickercashTransactModel->isReplenCourier) {
                $renderParams['usersList'] = $this->getCouriersList();
                $needUsersList = true;
            }
        }
        
        if ($pickercashTransactModel->isTypeTransferToAdmincash) {
            if ($pickercashTransactModel->isAccepted || $pickercashTransactModel->isRejected) {
                $needBanknotes = false;
            }
        }

        if ($pickercashTransactModel->isTypeExchange) {
            $needBanknotes = false;
            $needBanknotesExchangeForm = true;
        }
        
        $renderParams['needUsersList'] = $needUsersList;
        $renderParams['needBanknotes'] = $needBanknotes;
        $renderParams['needBanknotesExchangeForm'] = $needBanknotesExchangeForm;
        
        return $renderParams;
    }
    
    private function findPickercashTransactModel($id)
    {
        $model = PickercashTransact::find()
            ->with(['banknotes'])
            ->where(['id' => $id])
            ->one();
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
    
    private function getUsersList()
    {
        return CashdesksApi::getUsersListForPickercashTransactByDepartmentId($this->getDepartmentId());
    }
   
}
