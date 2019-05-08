<?php

namespace app\modules\cashdesks\controllers;

use Yii;

use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

use app\modules\cashdesks\filters\CashdesksPrepareFilter;
use app\modules\cashdesks\models\Banknotes;
use app\modules\cashdesks\models\ReplenType;
use app\modules\cashdesks\models\ReplenPurpose;
use app\modules\cashdesks\models\ExpenseType;
use app\modules\cashdesks\models\ExpenseTypeItem;
use app\modules\cashdesks\models\AdmincashTransact;
use app\modules\cashdesks\models\PickercashTransact;
use app\modules\cashdesks\models\CashdesksApi;
use app\modules\cashdesks\models\search\AdmincashTransactAcctabSearch;
use app\modules\cashdesks\models\search\AdmincashTransactDepartmentSearch;
use app\modules\cashdesks\models\search\PickercashTransactTransferSearch;
use app\modules\cashdesks\exceptions\CashdeskNotEnoughMoneyException;

class AdmincashController extends DefaultController
{
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'acctab-user-delete' => ['post'],
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
            'model' => $this->getAdmincash(),
            'createdTransfersToAdmincashCount' => $this->getCreatedTransfersToAdmincashCount(), 
        ]);
    }
    
    public function actionReplenCreate() 
    {
        $admincashTransactModel = new AdmincashTransact();
        $banknotesModel = new Banknotes();
        
        $admincashTransactModel->scenario = AdmincashTransact::SCENARIO_REPLEN_CREATE;
        $banknotesModel->scenario = Banknotes::SCENARIO_POSITIVE;
        
        $post = Yii::$app->request->post();
        if (
            $admincashTransactModel->load($post) &&
            $banknotesModel->load($post)
        ) {
            try {
                $success = AdmincashTransact::createReplen(
                    $admincashTransactModel, 
                    $this->getUserId(),
                    $this->getAdmincash(),
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
        
        return $this->render('replenCreate', [
            'admincashTransactModel' => $admincashTransactModel,
            'banknotesModel' => $banknotesModel,
            'replenTypesList' => $this->getReplenTypesList(),
            'replenPurposesList' => $this->getReplenPurposesList(),
        ]);
    }
    
    
    public function actionExpenseTypeSelect()
    {
        return $this->render('expenseTypeSelect', [
            'expenseTypesList' => $this->getExpenseTypesList(),
        ]);
    }
    
    public function actionExpenseCreate($id) 
    {
        $expenseTypeModel = $this->findExpenseTypeModel($id);
        if ($expenseTypeModel->isTypeAcctab) {
            // Выдача в подотчёт осуществляется другим способом.
            throw  new ForbiddenHttpException();
        }
        $admincashTransactModel = new AdmincashTransact();
        $banknotesModel = new Banknotes();
    
        $admincashTransactModel->setCreateScenarioOnExpenseType($expenseTypeModel);
        $banknotesModel->scenario = Banknotes::SCENARIO_POSITIVE;
        
        $post = Yii::$app->request->post();
        if (
            $admincashTransactModel->load($post) &&    
            $banknotesModel->load($post)
        ) {
            try {
                $success = AdmincashTransact::createExpense(
                    $admincashTransactModel, 
                    $this->getUserId(),
                    $this->getAdmincash(),
                    $banknotesModel,
                    $expenseTypeModel
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Операция совершена успешно.');
                    return $this->redirect(['index']);
                }    
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            }
        }
        
        $renderParams = $this->getRenderParamsForExpenseCreate(
            $admincashTransactModel, 
            $banknotesModel,
            $expenseTypeModel
        );
        return $this->render('expenseCreate', $renderParams);
    }
    
    
    public function actionChange()
    {
        $admincashTransactModel = new AdmincashTransact();
        $banknotesModel = new Banknotes();
        
        $admincashTransactModel->scenario = AdmincashTransact::SCENARIO_CHANGE_ADMINCASH_CREATE;
        
        $post = Yii::$app->request->post();
        if (
            $admincashTransactModel->load($post) &&
            $banknotesModel->load($post)
                
        ) {
            try {
                $success = AdmincashTransact::changeAdmincash(
                    $admincashTransactModel,
                    $this->getUserId(),
                    $this->getAdmincash(),
                    $banknotesModel
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Корректировка успешна.');
                    return $this->redirect(['index']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            }
        }
        
        return $this->render('change', [
            'admincashTransactModel' => $admincashTransactModel,
            'banknotesModel' => $banknotesModel,
        ]);
    }
    
    public function actionExchange() 
    {
        $admincashTransactModel = new AdmincashTransact();
        $banknotesModel = new Banknotes();
        
        $admincashTransactModel->scenario =  AdmincashTransact::SCENARIO_CHANGE_ADMINCASH_CREATE;
        $banknotesModel->scenario = Banknotes::SCENARIO_EXCHANGE;
        
        $post = Yii::$app->request->post();
        if (
            $admincashTransactModel->load($post) &&
            $banknotesModel->load($post)
        ) {
            try {
                $success = AdmincashTransact::exchangeAdmincash(
                    $admincashTransactModel, 
                    $this->getUserId(),
                    $this->getAdmincash(),
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
            'admincashTransactModel' => $admincashTransactModel,
            'banknotesModel' => $banknotesModel
        ]);
    }
    
    public function actionAcctabIndex()
    {
        $searchModel = new AdmincashTransactAcctabSearch($this->getDepartmentId());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('acctabIndex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'administratorsList' => $this->getAdministratorsList(),
            'usersList' => $this->getUsersListForAcctab(),
        ]);
    }
    
    public function actionAcctabUserCreate()
    {
        $admincashTransactModel = new AdmincashTransact();
        $banknotesModel = new Banknotes();
        
        $admincashTransactModel->scenario = AdmincashTransact::SCENARIO_ACCTAB_USER_CREATE;
        $banknotesModel->scenario = Banknotes::SCENARIO_POSITIVE;
        
        $post = Yii::$app->request->post();
        if (
            $admincashTransactModel->load($post) &&
            $banknotesModel->load($post)
        ) {
            try {
                $success = AdmincashTransact::createAcctabUser(
                    $admincashTransactModel,
                    $this->getUserId(),
                    $this->getAdmincash(),
                    $banknotesModel
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Выдача денег успешна.');
                    return $this->redirect(['acctab-index']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);  
            }
        }
        
        return $this->render('acctabUserCreate', [
            'admincashTransactModel' => $admincashTransactModel,
            'banknotesModel' => $banknotesModel,
            'usersList' => $this->getUsersListForAcctab(),
        ]);
    }
    
    public function actionAcctabUserReturn($id)
    {
        $admincashTransactModel = $this->findAdmincashTransactModel($id);
        $admincashTransactModel->scenario = AdmincashTransact::SCENARIO_ACCTAB_USER_RETURN;
        
        if (!$this->canAdministratorAdmincashTransactAction($admincashTransactModel)) {
            throw new ForbiddenHttpException();
        }
        if (!$admincashTransactModel->isTypeAcctab || !$admincashTransactModel->isCreated) {
            throw new ForbiddenHttpException();
        }
        
        if ($admincashTransactModel->load(Yii::$app->request->post())) {
            try {
                $success = AdmincashTransact::returnAcctabUser(
                    $admincashTransactModel,
                    $this->getUserId(),
                    $this->getAdmincash()
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Возврат денег успешен.');
                    return $this->redirect(['acctab-index']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);  
            }
        }
        
        return $this->render('acctabUserReturn', [
            'admincashTransactModel' => $admincashTransactModel,
        ]);
    }
    
    public function actionAccountableIndex()
    {
        return $this->render('accountableIndex', [
            'model' => $this->getAccountable(),
        ]);
    }
    
    
    public function actionAccountableReplenCreate() 
    {
        $admincashTransactModel = new AdmincashTransact();
        $banknotesModel = new Banknotes();
        
        $admincashTransactModel->scenario = AdmincashTransact::SCENARIO_ACCOUNTABLE_OPERATION_CREATE;
        $banknotesModel->scenario = Banknotes::SCENARIO_POSITIVE;
        
        $post = Yii::$app->request->post();
        if (
            $admincashTransactModel->load($post) &&
            $banknotesModel->load($post)
        ) {
            try {
                $success = AdmincashTransact::createAccountableReplen(
                    $admincashTransactModel,
                    $this->getUserId(),
                    $this->getAdmincash(),
                    $banknotesModel
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Пополенение кассы "Под отчёт" успешно');
                    return $this->redirect(['accountable-index']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);  
            }
        }
        
        return $this->render('accountableReplenCreate', [
            'admincashTransactModel' => $admincashTransactModel,
            'banknotesModel' => $banknotesModel,
        ]);
    }
    
    public function actionAccountableReturnCreate()
    {
        $admincashTransactModel = new AdmincashTransact();
        $banknotesModel = new Banknotes();
        
        $admincashTransactModel->scenario = AdmincashTransact::SCENARIO_ACCOUNTABLE_OPERATION_CREATE;
        $banknotesModel->scenario = Banknotes::SCENARIO_POSITIVE;
        
        $post = Yii::$app->request->post();
        if (
            $admincashTransactModel->load($post) &&
            $banknotesModel->load($post)
        ) {
            try {
                $success = AdmincashTransact::createAccountableReturn(
                    $admincashTransactModel,
                    $this->getUserId(),
                    $this->getAdmincash(),
                    $banknotesModel
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Изъятие денег из кассы "Под отчёт" успешно');
                    return $this->redirect(['accountable-index']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);  
            }
        }

        return $this->render('accountableReturnCreate', [
            'admincashTransactModel' => $admincashTransactModel,
            'banknotesModel' => $banknotesModel,
        ]);
    }

    public function actionPickercashTransferIndex()
    {
        $searchModel = new PickercashTransactTransferSearch($this->getDepartmentId());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('pickercashTransferIndex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'usersList' => $this->getUsersListPickercash(),
        ]);
    }
    
    public function actionPickercashTransferView($id)
    {
        $model = $this->findPickercashTransactModel($id);
        
        if (!$this->canAdministratorPickercashTransactAction($model)) {
            throw new ForbiddenHttpException();
        }
        
        return $this->render('pickercashTransferView', [
            'model' => $model,
        ]);
    }
    
    public function actionPickercashTransferAccept($id)
    {
        $pickercashTransactrModel = $this->findPickercashTransactModel($id);    
        $pickercashTransactrModel->scenario = PickercashTransact::SCENARIO_TRANSFER_TO_ADMINCASH_ACCEPT;
        
        if (!$this->canAdministratorPickercashTransactAction($pickercashTransactrModel)) {
            throw new ForbiddenHttpException();
        }
        if (!$pickercashTransactrModel->isCreated) {
            throw new ForbiddenHttpException();
        }
        
        if ($pickercashTransactrModel->load(Yii::$app->request->post())) {
            try {
                $success = PickercashTransact::acceptTransferToAdmincash(
                    $pickercashTransactrModel,
                    $this->getUserId()
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Перевод принят успешно.');
                    return $this->redirect(['pickercash-transfer-index']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);  
            }
        }
        
        return $this->render('pickercashTransferAccept', [
            'pickercashTransactModel' => $pickercashTransactrModel,
        ]);
    }
    
    public function actionPickercashTransferReject($id)
    {
        $pickercashTransactrModel = $this->findPickercashTransactModel($id);
        $pickercashTransactrModel->scenario = PickercashTransact::SCENARIO_TRANSFER_TO_ADMINCASH_REJECT;
        
        if (!$this->canAdministratorPickercashTransactAction($pickercashTransactrModel)) {
            throw new ForbiddenHttpException();
        }
        if (!$pickercashTransactrModel->isCreated) {
            throw new ForbiddenHttpException();
        }
        
        if ($pickercashTransactrModel->load(Yii::$app->request->post())) {
            try {
                $success = PickercashTransact::rejectTransferToAdmincash(
                    $pickercashTransactrModel,
                    $this->getUserId()
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Перевод отклонён.');
                    return $this->redirect(['pickercash-transfer-index']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);  
            }
        }
        
        return $this->render('pickercashTransferReject', [
            'pickercashTransactModel' => $pickercashTransactrModel,  
        ]);
    }
    
    public function actionHistory()
    {        
        $searchModel = new AdmincashTransactDepartmentSearch($this->getDepartmentId());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'administratorsList' => $this->getAdministratorsList(),
            'usersList' => $this->getUsersListAdmincash(),
            'usersEditList' => $this->getUsersEditListAdmincash(),
        ]);
    }
    
    public function actionTransactView($id)
    {
        $model = $this->findAdmincashTransactModel($id);
        
        if (!$this->canAdministratorAdmincashTransactAction($model)) {
            throw new ForbiddenHttpException();
        }
        
        return $this->render('transactView', [ 
            'model' => $model,
        ]);
    }
    
    public function actionTransactUpdate($id)
    {
        $admincashTransactModel = $this->findAdmincashTransactModel($id);
        $banknotesModel = $admincashTransactModel->banknotes;   
     
        if ($admincashTransactModel->isTypeExchange) {
            $banknotesModel->scenario = Banknotes::SCENARIO_EXCHANGE;
        } elseif (!$admincashTransactModel->isTypeChange) {
            // Во всех остальных случаях кроме корректировки и размена у нас могут 
            // быть только положительные значения.
            $banknotesModel->scenario = Banknotes::SCENARIO_POSITIVE;
        }
        $admincashTransactModel->setUpdateScenario(true);
        
        if (!$this->canAdministratorAdmincashTransactAction($admincashTransactModel)) {
            throw new ForbiddenHttpException();
        }
        if (
            $admincashTransactModel->isTypeExpense && 
            $admincashTransactModel->isExpenseAccdep &&
            !$admincashTransactModel->isCreated      
        ) {
            throw new ForbiddenHttpException();     
        }

        $post = Yii::$app->request->post();
        
        $needBanknotesLoad = true;

        if ($admincashTransactModel->isTypeTransferFromPickercash) {
            $needBanknotesLoad = false;
        }
        
        $loadSuccess = $admincashTransactModel->load($post);
        if ($needBanknotesLoad) {
            $loadSuccess = $loadSuccess && $banknotesModel->load($post);
        }
        
        if ($loadSuccess) {
            try {
                if ($admincashTransactModel->isTypeTransferFromPickercash) {
                    $success = AdmincashTransact::updateTransferFromPickercashByAdministrator(
                        $admincashTransactModel, 
                        $this->getUserId()
                    );
                } else {
                    $success = AdmincashTransact::updateOperation(
                        $admincashTransactModel,
                        $this->getUserId(),
                        $banknotesModel
                    );
                }
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
            'admincashTransactModel' => $admincashTransactModel,
            'banknotesModel' => $banknotesModel,
        ]);
    }
    
    public function actionTransactDelete($id)
    {
        $admincashTransactModel = $this->findAdmincashTransactModel($id);
        
        if (!$this->canAdministratorAdmincashTransactAction($admincashTransactModel)) {
            throw new ForbiddenHttpException();
        }
        if (
            $admincashTransactModel->isTypeExpense && 
            $admincashTransactModel->isExpenseAccdep &&
            !$admincashTransactModel->isCreated   
        ) {
            throw new ForbiddenHttpException();     
        }
        try {
            if (AdmincashTransact::revertOperation($admincashTransactModel)) {
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
    
    public function getRenderParamsForExpenseCreate(
        $admincashTransactModel,
        $banknotesModel,
        $expenseTypeModel
    ) {
        $renderParams = [
            'admincashTransactModel' => $admincashTransactModel,
            'banknotesModel' => $banknotesModel,
            'expenseTypeModel' => $expenseTypeModel,
        ];
        
        $needUsersList = false;
        $userIdRequired = false; // Обазятельно ли нужно указать пользователя.
        $needTypeValueText = false;
        $needExpenseTypeItemsList = false;
        
        if ($expenseTypeModel->isTypeSalary) {
            $renderParams['usersList'] = $this->getUsersListForSalaryPaymnet();
            $needUsersList = true;
            $userIdRequired = true;
        } elseif ($expenseTypeModel->isTypeSupplier) {
            $renderParams['usersList'] = $this->getSuppliersList();
            $needUsersList = true;
            $userIdRequired = true;
        } elseif ($expenseTypeModel->isTypeText || $expenseTypeModel->isTypeArray) {
            $renderParams['usersList'] = $this->getUsersListForCustomExpense();
            $needUsersList = true;
        }
        
        if ($expenseTypeModel->isTypeText) {
            $needTypeValueText = true;
        }
        
        if ($expenseTypeModel->isTypeSupplier || $expenseTypeModel->isTypeArray) {
            $renderParams['expenseTypeItemsList'] = $this->getExpenseTypeItemsList($expenseTypeModel->id);
            $needExpenseTypeItemsList = true;
        }
        
        $renderParams['needUsersList'] = $needUsersList;
        $renderParams['userIdRequired'] = $userIdRequired;
        $renderParams['needTypeValueText'] = $needTypeValueText;
        $renderParams['needExpenseTypeItemsList'] = $needExpenseTypeItemsList;
        
        return $renderParams;
    }
    
    
    public function getRenderParamsForTransactUpdate(
        $admincashTransactModel, 
        $banknotesModel
    ) {
        $renderParams = [
            'admincashTransactModel' => $admincashTransactModel,
            'banknotesModel' => $banknotesModel,
        ];
        
        $needUsersList = false;
        $userIdRequired = false;
        $needTypeIdsList = false;
        $needTypeValuesList = false;
        $needTypeValueText = false;
        $needBanknotes = true;
        $needBanknotesExchangeForm = false;
        $needStatesList = false;
        
        
        if ($admincashTransactModel->isTypeReplen) {
            $renderParams['typeIdsList'] = $this->getReplenTypesList();
            $renderParams['typeValuesList'] = $this->getReplenPurposesList();
            $needTypeIdsList = true;
            $needTypeValuesList = true;
        }
        
        if ($admincashTransactModel->isTypeExpense) {
            $expenseTypeModel = $admincashTransactModel->expenseType;
                    
            if ($expenseTypeModel->isTypeSalary) {
                $renderParams['usersList'] = $this->getUsersListForSalaryPaymnet();
                $needUsersList = true;
                $userIdRequired = true;
            } elseif ($expenseTypeModel->isTypeSupplier) {
                $renderParams['usersList'] = $this->getSuppliersList();
                $needUsersList = true;
                $userIdRequired = true;
            } elseif ($expenseTypeModel->isTypeText || $expenseTypeModel->isTypeArray) {
                $renderParams['usersList'] = $this->getUsersListForCustomExpense();
                $needUsersList = true;
            }
            
            if ($expenseTypeModel->isTypeSupplier || $expenseTypeModel->isTypeArray) {
                $renderParams['typeValuesList'] = $this->getExpenseTypeItemsList($expenseTypeModel->id);
                $needTypeValuesList = true;
            }
            
            $renderParams['expenseTypeModel'] = $expenseTypeModel;
        }
                
        if ($admincashTransactModel->isTypeExchange) {
            $needBanknotes = false;
            $needBanknotesExchangeForm = true; 
        }
        
        if ($admincashTransactModel->isTypeAcctab) {
            $renderParams['usersList'] = $this->getUsersListForAcctab();
            $renderParams['statesList'] = AdmincashTransact::getStatesArrayAcctabUser();
            $needUsersList = true;
            $needStatesList = true;
        }
        
        if ($admincashTransactModel->isTypeTransferFromPickercash) {
            $renderParams['statesList'] = AdmincashTransact::getStatesArrayTransferFromPickercashUpdateAdministrator();
            $needStatesList = true;
            $needBanknotes = false;
        }
        
        $renderParams['needUsersList'] = $needUsersList;
        $renderParams['userIdRequired'] = $userIdRequired;
        $renderParams['needTypeIdsList'] = $needTypeIdsList;
        $renderParams['needTypeValuesList'] = $needTypeValuesList;
        $renderParams['needTypeValueText'] =$needTypeValueText;
        $renderParams['needBanknotes'] = $needBanknotes;
        $renderParams['needBanknotesExchangeForm'] = $needBanknotesExchangeForm;
        $renderParams['needStatesList'] = $needStatesList;
        
        return $renderParams;
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
    
    private function findExpenseTypeModel($id)
    {
        $model = ExpenseType::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        return $model;
    }
    
    private function processNotEnoughMoneyException($e)
    {
        Yii::$app->session->setFlash('error', $e->getMessage());
    }
    
    private function getReplenTypesList()
    {
        $replenTypes = ReplenType::find()->all();
        return ArrayHelper::map($replenTypes, 'id', 'name');
    }
    
    private function getReplenPurposesList()
    {
        $replenPurposes = ReplenPurpose::find()->all();
        return ArrayHelper::map($replenPurposes, 'name', 'name');
    }
    
    public function getExpenseTypesList()
    {
        // Выдача денег под отчёт оусществляется другим способом.
        $expenseTypes = ExpenseType::find()
                ->where(['<>', 'type', ExpenseType::TYPE_ACCTAB]) 
                ->all();
        return ArrayHelper::map($expenseTypes, 'id', 'name');
    }
      
    private function getExpenseTypeItemsList($expenseTypeId) 
    {
        $expenseTypeItems = ExpenseTypeItem::findAll(['expense_type_id' => $expenseTypeId]);
        return ArrayHelper::map($expenseTypeItems, 'value', 'value');
    }
    
    private function getCreatedTransfersToAdmincashCount()
    {
        return PickercashTransact::find()
            ->where([
               'depart_id' => $this->getDepartmentId(),
               'type' => PickercashTransact::TYPE_TRANSFER_TO_ADMINCASH,
               'state' => PickercashTransact::STATE_CREATED,
            ])->count();
    }
    
    private function getUsersListForSalaryPaymnet()
    {
        return CashdesksApi::getUsersListForSalaryPaymnetByDepartmentId($this->getDepartmentId());
    }
   
    private function getUsersListForAcctab()
    {
        return CashdesksApi::getUsersListForAcctabByDepartmentId($this->getDepartmentId());
    }
    
    private function getUsersListForCustomExpense()
    {
        $emptyUser = ['' => null];
        $usersList = CashdesksApi::getUsersListForCustomExpenseByDepartmentId($this->getDepartmentId());
        return ArrayHelper::merge($emptyUser, $usersList);
    }
   
    private function getSuppliersList()
    {
        return CashdesksApi::getSuppliersListByDepartmentId($this->getDepartmentId());
    }
    
    private function getAdministratorsList()
    {
        return CashdesksApi::getAdministratorsListByDepartmentId($this->getDepartmentId());
    }
    
    private function getUsersListAdmincash()
    {
        return CashdesksApi::getUsersListForAdmincashTransactByDepartmentId($this->getDepartmentId());
    }
    
    private function getUsersEditListAdmincash()
    {
        return CashdesksApi::getUsersEditListForAdmincashTransactByDepartmentId($this->getDepartmentId());
    }
    
    private function getUsersListPickercash()
    {
        return CashdesksApi::getUsersListForPickercashTransactByDepartmentId($this->getDepartmentId());
    }   
        
}
