<?php

namespace app\modules\cashdesks\controllers;

use Yii;

use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use app\modules\cashdesks\filters\UserFilter;
use app\modules\cashdesks\models\Banknotes;
use app\modules\cashdesks\models\ReplenType;
use app\modules\cashdesks\models\ReplenPurpose;
use app\modules\cashdesks\models\ExpenseTypeItem;
use app\modules\cashdesks\models\AdmincashTransact;
use app\modules\cashdesks\models\PickercashTransact;
use app\modules\cashdesks\models\AccountableTransact;
use app\modules\cashdesks\models\CashdesksApi;
use app\modules\cashdesks\models\search\AdmincashTransactBaseSearch;
use app\modules\cashdesks\models\search\PickercashTransactBaseSearch;
use app\modules\cashdesks\models\search\AccountableTransactBaseSearch;
use app\modules\cashdesks\exceptions\CashdeskNotEnoughMoneyException;

class HistoryController extends DefaultController
{
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'admincash-transact-delete' => ['post'],
                    'pickercash-transact-delete' => ['post'],
                    'accountable-transact-delete' => ['post'],
                ],
            ],
            'userFilter' => [
                'class' => UserFilter::className(),
            ],
        ];
    } 
    
    public function actionIndex() 
    {
        return $this->render('index');
    }
    
    public function actionAdmincashHistory()
    {
        $searchModel = new AdmincashTransactBaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('admincashHistory', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'departmentsList' => $this->getDepartmentsList(),
            'administratorsList' => $this->getAdministratorsList(),
            'usersList' => $this->getUsersListForAdmincashTransact(),
            'usersEditList' => $this->getUsersEditListForAdmincashTransact(),
        ]);
    }
    
    public function actionPickercashHistory()
    {
        $searchModel = new PickercashTransactBaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('pickercashHistory', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'departmentsList' => $this->getDepartmentsList(),
            'pickersList' => $this->getPickersList(),
            'usersList' => $this->getUsersListForPickercashTransact(),
        ]);
    }
    
    public function actionAccountableHistory()
    {
        $searchModel = new AccountableTransactBaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                
        return $this->render('accountableHistory', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'departmentsList' => $this->getDepartmentsList(),
            'pickersList' => $this->getPickersList(),
            'usersList' => $this->getUsersListForAccountableTransact(),
        ]);
    }
    
    public function actionAdmincashTransactView($id)
    {
        return $this->render('admincashTransactView', [
            'model' => $this->findAdmincashTransactModel($id),
        ]);
    }
    
    public function actionAdmincashTransactUpdate($id)
    {
        $admincashTransactModel = $this->findAdmincashTransactModel($id);
        $banknotesModel = $admincashTransactModel->banknotes;   
     
        if ($admincashTransactModel->isTypeExchange) {
            $banknotesModel->scenario = Banknotes::SCENARIO_EXCHANGE;
        } elseif (!$admincashTransactModel->isTypeChange) {
            $banknotesModel->scenario = Banknotes::SCENARIO_POSITIVE;
        }
        
        $admincashTransactModel->setUpdateScenario();
        
        $post = Yii::$app->request->post();
        
        $needBanknotesLoad = true;

        $loadSuccess = $admincashTransactModel->load($post);
        if ($needBanknotesLoad) {
            $loadSuccess = $loadSuccess && $banknotesModel->load($post);
        }
        
        if ($loadSuccess) {
            try {
                $success = AdmincashTransact::updateOperation(
                    $admincashTransactModel,
                    $this->getUserId(),
                    $banknotesModel
                );
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Обновление успешно');
                    return $this->redirect(['admincash-history']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            }
        }
            
        return $this->render('admincashTransactUpdate',[
            'admincashTransactModel' => $admincashTransactModel,
            'banknotesModel' => $banknotesModel,
        ]);
    }
    
    public function actionAdmincashTransactDelete($id)
    {
        $admincashTransacModel = $this->findAdmincashTransactModel($id);
        try {
            if (AdmincashTransact::revertOperation($admincashTransacModel)) {
                Yii::$app->session->setFlash('success', 'Отмена операции успешна.');
                return $this->redirect(['admincash-history']);
            } else {
                Yii::$app->session->setFlash('error', 'Произошла ошибка');
                return $this->redirect(['admincash-history']);
            }
        }
        catch (CashdeskNotEnoughMoneyException $e) {
            $this->processNotEnoughMoneyException($e);
            return $this->redirect(['admincash-history']);
        }
    }
    
    public function actionPickercashTransactView($id)
    {
        return $this->render('pickercashTransactView', [
            'model' => $this->findPickercashTransactModel($id),
        ]);
    }
    
    
    public function actionPickercashTransactUpdate($id)
    {
        $pickercashTransactModel = $this->findPickercashTransactModel($id);
        $banknotesModel = $pickercashTransactModel->banknotes;
        
        if ($pickercashTransactModel->isTypeTransferToAdmincash) {
            $admincashTransact = $pickercashTransactModel->admincashTransact;
            return $this->redirect(['admincash-transact-update', 'id' => $admincashTransact->id]);
        }
        
        $pickercashTransactModel->setUpdateScenario();
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
                    return $this->redirect(['pickercash-history']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            }
        }
            
        return $this->render('pickercashTransactUpdate', [
            'pickercashTransactModel' => $pickercashTransactModel,
            'banknotesModel' => $banknotesModel,
        ]);
    }
    
    public function actionPickercashTransactDelete($id)
    {   
        $pickercashTransactModel = $this->findPickercashTransactModel($id);
        try {
            if (PickercashTransact::revertOperation($pickercashTransactModel)) {
                Yii::$app->session->setFlash('success', 'Отмена операции успешна.');
                return $this->redirect(['pickercash-history']);
            } else {
                Yii::$app->session->setFlash('error', 'Произошла ошибка');
                return $this->redirect(['pickercash-history']);
            }
        }
        catch (CashdeskNotEnoughMoneyException $e) {
            $this->processNotEnoughMoneyException($e);
            return $this->redirect(['pickercash-history']);
        }        
    }
    
    public function actionAccountableTransactView($id)
    {
        return $this->render('accountableTransactView', [
            'model' => $this->findAccountableTransactModel($id),
        ]);
    }
    
    public function actionAccountableTransactUpdate($id)
    {
        $accountableTransactModel = $this->findAccountableTransactModel($id);
        
        if (
            $accountableTransactModel->isTypeReplen || 
            $accountableTransactModel->isTypeReturn
        ) {
            $admincashTransact = $accountableTransactModel->admincashTransact;
            return $this->redirect(['admincash-transact-update', 'id' => $admincashTransact->id]);    
        }
        
        $accountableTransactModel->setUpdateScenario();
        if ($accountableTransactModel->load(Yii::$app->request->post())) {
            try {
                $success = AccountableTransact::updateOperation($accountableTransactModel);
                if ($success) {
                    Yii::$app->session->setFlash('success', 'Обновление успешно'); 
                    return $this->redirect(['accountable-history']);
                }
            }
            catch (CashdeskNotEnoughMoneyException $e) {
                $this->processNotEnoughMoneyException($e);
            }
        }

        return $this->render('accountableTransactUpdate', [
            'accountableTransactModel' => $accountableTransactModel,
        ]);
    }   
    
    public function actionAccountableTransactDelete($id)
    {
        $model = $this->findAccountableTransactModel($id);
        try {
            if (AccountableTransact::revertOperation($model)) {
                Yii::$app->session->setFlash('success', 'Отмена операции успешна.');
                return $this->redirect(['accountable-history']);
            } else {
                Yii::$app->session->setFlash('error', 'Произошла ошибка');
                return $this->redirect(['accountable-history']);
            }
        }
        catch (CashdeskNotEnoughMoneyException $e) {
            $this->processNotEnoughMoneyException($e);
        }
    }
    
    public function getRenderParamsForAdmincashTransactUpdate(
        $admincashTransactModel, 
        $banknotesModel
    ) {        
        $departmentId = $admincashTransactModel->depart_id;
        
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
                $renderParams['usersList'] = $this->getUsersListForSalaryPaymnet($departmentId);
                $needUsersList = true;
                $userIdRequired = true;
            } elseif ($expenseTypeModel->isTypeSupplier) {
                $renderParams['usersList'] = $this->getSupplierList($departmentId);
                $needUsersList = true;
                $userIdRequired = true;
            } elseif ($expenseTypeModel->isTypeText || $expenseTypeModel->isTypeArray) {
                $renderParams['usersList'] = $this->getUsersListForCustomExpense($departmentId);
                $needUsersList = true;
            }
            
            if ($expenseTypeModel->isTypeSupplier || $expenseTypeModel->isTypeArray) {
                $renderParams['typeValuesList'] = $this->getExpenseTypeItemsList($expenseTypeModel->id);
                $needTypeValuesList = true;
            }
            
            if ($expenseTypeModel->isTypeText) {
                $needTypeValueText = true;    
            }
            
             if ($expenseTypeModel->isTypeAccdep) {
                 $needStatesList = true;
                 $renderParams['statesList'] = AdmincashTransact::getStatesArray();
             }
            
            $renderParams['expenseTypeModel'] = $expenseTypeModel;
        }
        
        if ($admincashTransactModel->isTypeAcctab) {
            $renderParams['usersList'] = $this->getUsersListForAcctab($departmentId);
            $renderParams['statesList'] = AdmincashTransact::getStatesArrayAcctabUser();
            $needUsersList = true;
            $needStatesList = true;
        }
        
        if ($admincashTransactModel->isTypeExchange) {
            $needBanknotes = false;
            $needBanknotesExchangeForm = true; 
        }

        if ($admincashTransactModel->isTypeTransferFromPickercash) {
            $renderParams['statesList'] = AdmincashTransact::getStatesArray();
            $needStatesList = true;
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
    
    
    public function getRenderParamsForPickercashTransactUpdate(
        $pickercashTransactModel, 
        $banknotesModel
    ) {
        $departmentId =$pickercashTransactModel->depart_id;
        
        $renderParams = [
            'pickercashTransactModel' => $pickercashTransactModel,
            'banknotesModel' => $banknotesModel,
        ];
        
        $needUsersList = false; 
        $needBanknotes = true;
        $needBanknotesExchangeForm = false;

   
        if ($pickercashTransactModel->isTypeReplen) {
            if ($pickercashTransactModel->isReplenCourier) {
                $needUsersList = true;
                $renderParams['usersList'] = $this->getCouriersListByDepartmentId($departmentId);
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
    
    public function getRenderParamsForAccountableTransactUpdate($accountableTransactModel) 
    {
        $departmentId = $accountableTransactModel->depart_id;
        
        $renderParams = [
            'accountableTransactModel' => $accountableTransactModel,
        ];
        
        $needUsersList = false;

        if ($accountableTransactModel->isAcctab) {
            if ($accountableTransactModel->isAcctabCourier) {
                $renderParams['usersList'] = $this->getCouriersListByDepartmentId($departmentId);
                $needUsersList = true;
            }
        }
        
        $renderParams['needUsersList'] = $needUsersList;
            
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
    
    private function getDepartmentsList()
    {
        return CashdesksApi::getDepartmentsList();
    }
    
     private function getExpenseTypeItemsList($expenseTypeId) 
    {
        $expenseTypeItems = ExpenseTypeItem::findAll(['expense_type_id' => $expenseTypeId]);
        return ArrayHelper::map($expenseTypeItems, 'value', 'value');
    }
    
    private function getUsersListForSalaryPaymnet($departmentId)
    {
        return CashdesksApi::getUsersListForSalaryPaymnetByDepartmentId($departmentId);
    }
   
    private function getUsersListForAcctab($departmentId)
    {
        return CashdesksApi::getUsersListForAcctabByDepartmentId($departmentId);
    }
    
    private function getUsersListForCustomExpense($departmentId)
    {
        $emptyUser = ['' => null];
        $usersList = CashdesksApi::getUsersListForCustomExpenseByDepartmentId($departmentId);
        return ArrayHelper::merge($emptyUser, $usersList);
    }
   
    private function getSupplierList($departmentId)
    {
        return CashdesksApi::getSuppliersListByDepartmentId($departmentId);
    }
    
    private function getAdministratorsList()
    {
        return CashdesksApi::getAdminstratorsList();
    }
    
    private function getPickersList()
    {
        return CashdesksApi::getPickersList();
    }
    
    private function getCouriersListByDepartmentId($departmentId)
    {
        return CashdesksApi::getCouriersListByDepartmentId($departmentId);
    }
   
    
    private function getUsersListForAdmincashTransact()
    {
        return CashdesksApi::getUsersEditListForAdmincashTransact();
    }
    
    private function getUsersEditListForAdmincashTransact()
    {
        return CashdesksApi::getUsersEditListForAdmincashTransact();
    }
    
    private function getUsersListForPickercashTransact()
    {
        return CashdesksApi::getUsersListForPickercashTransact();
    }
    
    private function getUsersListForPickercashTransactByDepartmentId($departmentId)
    {
        return CashdesksApi::getUsersListForPickercashTransactByDepartmentId($departmentId);    
    }
    
    private function getUsersListForAccountableTransact()
    {
        return CashdesksApi::getUsersListForAccountableTransact();
    }
    
}
