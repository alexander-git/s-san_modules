<?php

namespace app\modules\picker\controllers;

use Yii;

use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use app\modules\picker\filters\PickerWorkPrepareFilter;
use app\modules\picker\models\PickerApi;
use app\modules\picker\models\Shifts;
use app\modules\picker\models\ShiftsPicker;
use app\modules\picker\models\ShiftsCourier;
use app\modules\picker\models\Banknotes;
use app\modules\picker\models\OptionsVal;
use app\modules\picker\models\search\ShiftsCourierSearch;
use app\modules\picker\models\search\ShiftsCourierSummarySearch;
use app\modules\picker\models\form\ShiftsPickerCloseForm;

class ViewController extends DefaultController
{
    
    public $defaultAction = 'pickerIndex';
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'shiftPickerOpen' => ['post'],
                    'shiftCourierDelete' => ['post'],
                    'shiftCheckigByMainPickerStart' => ['post'],
                ],
            ],            
            'pickerWork' => [
                'class' => PickerWorkPrepareFilter::className(),
            ],  
        ];
    }
    
    
    /**
     * Для вызова в виде.
     */
    public function getShiftPickerStateInfo()
    {
        $info = new \stdClass();
        $info->shiftPickerDateOpen = Yii::$app->formatter->format(
            $this->shiftPicker->date_open, 
            ['datetime', 'php:d-m-Y H:i:s']
        );
        $info->isMainPicker = $this->isMainPicker(); 
        
        return $info;
    }
    
    /**
     * Центральная страница работы со сменой комплектовщика.
     */
    public function actionPickerIndex()
    {
        if (!$this->isShiftPickerOpened()) {
            return $this->redirect(['shift-picker-index']);
        }
        
        if ($this->isShiftCheckingByMainPicker()) {
            if ($this->isMainPicker()) {
                return $this->redirect(['shift-index']);
            } else {
                // В этом случае смена комплектовщика не должна быть открыта.
                throw new \LogicException();
            }
        }
        
        if (!$this->canShiftPickerAction()) {
            throw new ForbiddenHttpException();
        }
        
        $searchModel = new ShiftsCourierSearch($this->getShiftPickerId());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('pickerIndex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Стартовая страница для открытия смены комплектовщика. Если открытие 
     * смены в данный момент невозможно рользователю будет показано сообщение.
     */
    public function actionShiftPickerIndex()
    {
        if (!$this->canShiftPickerIndex()) {
            throw new ForbiddenHttpException();
        }
        
        return $this->render('shiftPickerIndex', [
            'isShiftPickerClosed' => $this->isShiftPickerClosed(),
            'isShiftCheckingByMainPicker' => $this->isShiftCheckingByMainPicker(),
        ]);
    }
    
    public function actionShiftPickerOpen() 
    {
        if (!$this->canShiftPickerOpen()) {
            throw new ForbiddenHttpException();
        }
                
        try {
            // Открыта ли суточная смена.
            ShiftsPicker::openShiftPicker($this->getDepartmentId(), $this->getPickerId());
            Yii::$app->session->setFlash('success', 'Смена комплектовщика успешно открыта');
            return $this->redirect(['picker-index']);
        } 
        catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Произошла ошибка');
            return $this->redirect(['shift-picker-index']);
        }
    }
    
        
    // Действия в рамках смены комплектовщика.
    ////////////////////////////////////////////////////////////////////////////
    
    public function actionShiftPickerClose()
    {
        if ($this->isLastPicker()) {
            // Проследний комплектовщик.
            if ($this->isMainPicker()) {
                return $this->redirect(['shift-checking-preview']);
            } else {
                throw new \LogicException();
            }
        }
        
        if (!$this->canShiftPickerClose()) {
            throw new ForbiddenHttpException();
        }
                
        $shift = $this->getShift();
        $shiftPicker = $this->getShiftPicker();
        $model = new ShiftsPickerCloseForm($shift, $shiftPicker);
        
        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->close();
                
                Yii::$app->session->setFlash('success', 'Смена комплектовщика успешно закрыта.');
                return $this->redirect(['shift-picker-closed', 'id' => $shiftPicker->id]);
            } 
            catch (\Exception $e) {
                Yii::$app->session->setFlash('success', 'Произошла ошибка.');
            }
        }
                    
        return $this->render('shiftPickerClose', [
            'model' => $model,
            'pickersList' => $this->getPickersList(false),
        ]);
    }
     
    public function actionShiftPickerClosed($id)
    {
        $shiftPicker = $this->findShiftsPickerModel($id);
        if (!$this->canShiftPickerClosed($shiftPicker)) {
            throw new ForbiddenHttpException();
        }
        
        return $this->render('shiftPickerClosed');
    }
    
    /**
     * Переход к закрытию суточной смены.
     */ 
    public function actionShiftCheckingPreview()
    {
        if (!$this->canShiftCheckingPreview()) {
            throw new ForbiddenHttpException();     
        }
        
        $shiftsCourierOpenedCount = $this->getShiftsCourierOpenedCount(false);
        
        return $this->render('shiftCheckingPreview', [
            'shiftsCourierOpenedCount' => $shiftsCourierOpenedCount,
        ]);
    }
    
    public function actionShiftCheckingByMainPickerStart()
    {
        if (!$this->canShiftCheckingByMainPickerStart()) {
            throw new ForbiddenException();
        }
        $shift = $this->getShift();
        $shift->startCheckingByMainPicker();
        if ($shift->save()) {
            return $this->redirect(['shift-courier-pickup-close-fill']);
        } else {
            throw new \Exception();
        }
    }

    public function actionShiftCourierOpenDefault()
    {
        if (!$this->canShiftPickerAction()) {
            throw new ForbiddenHttpException();
        }
        
        $model = new ShiftsCourier();
        $model->scenario = ShiftsCourier::SCENARIO_OPEN_DEFAULT;
        
        if ($model->load(Yii::$app->request->post())) {
            $model->courier_name = PickerApi::getCourierName($model->courier_id);
            $model->courier_phone = PickerApi::getCourierPhone($model->courier_id); 
            $model->shifts_id = $this->getShiftId();
            $model->shifts_picker_id = $this->getShiftPickerId();
            $model->open();
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Смена курьера успешно открыта.');
                return $this->redirect(['picker-index']);
            } 
        }
        
        return $this->render('shiftCourierOpenDefault', [
            'model' => $model,
            'couriersList' => $this->getFreeCouriersList(),
        ]);
    }
    
    public function actionShiftCourierOpenAdditional() 
    {
        if (!$this->canShiftPickerAction()) {
            throw new ForbiddenHttpException();
        }
        
        $model = new ShiftsCourier();
        $model->scenario = ShiftsCourier::SCENARIO_OPEN_ADDITIONAL;
        
        if ($model->load(Yii::$app->request->post())) {
            $model->courier_id = null;
            $model->type_courier = ShiftsCourier::TYPE_COURIER_ADDITIONAL;
            $model->shifts_id = $this->getShiftId();
            $model->shifts_picker_id = $this->getShiftPickerId();
            $model->open();
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Смена курьера успешно открыта.');
                return $this->redirect(['picker-index']);
            } 
        }
        
        return $this->render('shiftCourierOpenAdditional', [
            'model' => $model,
        ]); 
    }
    
    public function actionShiftCourierCloseFill($id)
    {
        $model = $this->findShiftsCourierModel($id);
        
        if (!$this->canShiftPickerActionCourier($model)) {
            throw new ForbiddenHttpException();
        }
        
        if ($model->isTypeCourierDefault) {
            $model->scenario = ShiftsCourier::SCENARIO_FILL_DEFAULT;
        } elseif ($model->isTypeCourierAdditional) {
            $model->scenario = ShiftsCourier::SCENARIO_FILL_ADDITIONAL;
        }
 
        if (
            $model->load(Yii::$app->request->post()) &&
            $model->save()
        ) {
            return $this->redirect(['shift-courier-close-banknotes', 'id' => $id]);
        }
        
        return $this->render('shiftCourierCloseFill', [
            'model' => $model,
        ]);
    }
    
    public function actionShiftCourierCloseBanknotes($id)
    {
        $shiftsCourierModel = $this->findShiftsCourierModel($id);
        $banknotesModel = $this->getBanknotesModel($id);
        
        if (!$this->canShiftPickerActionCourier($shiftsCourierModel)) {
            throw new ForbiddenHttpException();
        }
        
        if (
            $banknotesModel->load(Yii::$app->request->post()) &&
            $banknotesModel->save()
        ) {
            if (!$shiftsCourierModel->isClosed) {
                $shiftsCourierModel->close();
            }
            if ($this->takeCashFromCourier($shiftsCourierModel, $banknotesModel)) {
                return $this->redirect(['shift-courier-close-summary', 'id' => $id]);   
            }            
        }
        
        return $this->render('shiftCourierCloseBanknotes', [
            'shiftsCourierModel' => $shiftsCourierModel,
            'banknotesModel' => $banknotesModel,
        ]);
    }
    
    public function actionShiftCourierCloseSummary($id) 
    {
        $shiftsCourierModel = $this->findShiftsCourierModel($id);
        $banknotesModel = $this->getBanknotesModel($id);
        
        if (
            !$this->canShiftPickerActionCourier($shiftsCourierModel) || 
            $shiftsCourierModel->isOpened
        ) {
            throw new ForbiddenHttpException();
        }
        
        return $this->render('shiftCourierCloseSummary', [
            'shiftsCourierModel' => $shiftsCourierModel,
            'banknotesModel' => $banknotesModel, 
        ]);
    }
    
    
    public function actionShiftCourierUpdateDefault($id) 
    {
        $model = $this->findShiftsCourierModel($id);
        
        if (!$this->canShiftPickerActionCourier($model) || $model->isClosed) {
            throw new ForbiddenHttpException();
        }
        
        $model->scenario = ShiftsCourier::SCENARIO_UPDATE_DEFAULT;
        
        if ($model->load(Yii::$app->request->post())) {
            $model->courier_name = PickerApi::getCourierName($model->courier_id);
            $model->courier_phone = PickerApi::getCourierPhone($model->courier_id); 
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Смена курьера успешно обновлена.');
                return $this->redirect(['picker-index']);
            }
        }
        
        // Добавим в список самого курьера.
        $currentCourier = [$model->courier_id => $model->courier_name];
        $couriersList = ArrayHelper::merge($currentCourier, $this->getFreeCouriersList());
        
        return $this->render('shiftCourierUpdateDefault', [
            'model' => $model,
            'couriersList' => $couriersList,
        ]); 
    }
    
    public function actionShiftCourierUpdateAdditional($id)
    {
        $model = $this->findShiftsCourierModel($id);
        
        if (!$this->canShiftPickerActionCourier($model) || $model->isClosed) {
            throw new ForbiddenHttpException();
        }
        
        $model->scenario = ShiftsCourier::SCENARIO_UPDATE_ADDITIONAL;
        
        if ($model->load(Yii::$app->request->post())) {
            $model->courier_id = null;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Смена курьера успешно обновлена.');
                return $this->redirect(['picker-index']);
            }
        }
        
        return $this->render('shiftCourierUpdateAdditional', [
            'model' => $model,
        ]); 
    }
    
    public function actionShiftCourierDelete($id)
    {
        $model = $this->findShiftsCourierModel($id);
        
        if (!$this->canShiftPickerActionCourier($model) || $model->isClosed) {
            throw new ForbiddenHttpException();
        }
                
        $model->delete();
        return $this->redirect(['picker-index']);
    }
    
    
    // Действия в рамках суточной смены.
    ////////////////////////////////////////////////////////////////////////////
    
    /**
     * Центаральная страница работы с суточной сменой.
     */
    public function actionShiftIndex() 
    {
        if (!$this->canShiftAction()) {
            throw new ForbiddenHttpException();   
        }
        
        $shiftsCourierPickupModel = $this->findShiftsCourierPickupModel();
                
        $searchModel = new ShiftsCourierSummarySearch();
        $dataProvider = $searchModel->search(
            $this->getShiftId(),
            Yii::$app->request->queryParams
        );
        
        return $this->render('shiftIndex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'shiftsCourierPickupModel' => $shiftsCourierPickupModel,
        ]);
        
    }
    
    public function actionShiftCourierPickupCloseFill() 
    {
        $model = $this->findShiftsCourierPickupModel();

        if (!$this->canShiftActionCourier($model)) {
            throw new ForbiddenHttpException(); 
        }
        
        $model->scenario = ShiftsCourier::SCENARIO_FILL_PICKUP;        
        if (
            $model->load(Yii::$app->request->post()) &&
            $model->save()
        ) {
            return $this->redirect(['shift-courier-pickup-close-banknotes']);
        }
        
        return $this->render('shiftCourierPickupCloseFill', [
            'model' => $model,
        ]);
    }
    
    public function actionShiftCourierPickupCloseBanknotes()
    {
        $shiftsCourierModel = $this->findShiftsCourierPickupModel();
        $banknotesModel = $this->getBanknotesModel($shiftsCourierModel->id);
        
        if (!$this->canShiftActionCourier($shiftsCourierModel)) {
            throw new ForbiddenHttpException(); 
        }
                
        if (
            $banknotesModel->load(Yii::$app->request->post()) &&
            $banknotesModel->save()
        ) {
            if (!$shiftsCourierModel->isClosed) {
                $shiftsCourierModel->close();
            }
            if ($this->takeCashFromCourier($shiftsCourierModel, $banknotesModel)) {            
                return $this->redirect(['shift-courier-pickup-close-summary']);
            }
        }
        
        return $this->render('shiftCourierPickupCloseBanknotes', [
            'shiftsCourierModel' => $shiftsCourierModel,
            'banknotesModel' => $banknotesModel,
        ]);
    }
    
    public function actionShiftCourierPickupCloseSummary()
    {
        $shiftsCourierModel = $this->findShiftsCourierPickupModel();
        $banknotesModel = $this->getBanknotesModel($shiftsCourierModel->id);
        
        if (
            !$this->canShiftActionCourier($shiftsCourierModel) ||
            $shiftsCourierModel->isOpened
        ) {
            throw new ForbiddenHttpException(); 
        }
                
        return $this->render('shiftCourierPickupCloseSummary', [
            'shiftsCourierModel' => $shiftsCourierModel,
            'banknotesModel' => $banknotesModel, 
        ]);
    }
    
    public function actionShiftCourierCheckFill($id) 
    {
        $model = $this->findShiftsCourierModel($id);
   
        if (!$this->canShiftActionCourier($model)) {
            throw new ForbiddenHttpException();
        }
        
        if ($model->isTypeCourierDefault) {
            $model->scenario = ShiftsCourier::SCENARIO_FILL_DEFAULT;
        } elseif ($model->isTypeCourierAdditional) {
            $model->scenario = ShiftsCourier::SCENARIO_FILL_ADDITIONAL;
        }
        
        if (
            $model->load(Yii::$app->request->post()) &&
            $model->save()
        ) {
            return $this->redirect(['shift-courier-check-banknotes', 'id' => $id]);
        }
        
        return $this->render('shiftCourierCheckFill', [
            'model' => $model,
        ]);
    }
    
    public function actionShiftCourierCheckBanknotes($id)
    {
        $shiftsCourierModel = $this->findShiftsCourierModel($id);
        $banknotesModel = $this->getBanknotesModel($id);
        
        if (!$this->canShiftActionCourier($shiftsCourierModel)) {
            throw new ForbiddenHttpException();
        }
        
        if (
            $banknotesModel->load(Yii::$app->request->post()) &&
            $banknotesModel->save()
        ) {
            $shiftsCourierModel->cash = $banknotesModel->sum;
            if ($this->takeCashFromCourier($shiftsCourierModel, $banknotesModel)) {
                return $this->redirect(['shift-courier-check-summary', 'id' => $id]);   
            }            
        }
        
        return $this->render('shiftCourierCheckBanknotes', [
            'shiftsCourierModel' => $shiftsCourierModel,
            'banknotesModel' => $banknotesModel,
        ]);
    }
    
    public function actionShiftCourierCheckSummary($id)
    {
        $shiftsCourierModel = $this->findShiftsCourierModel($id);        
        $banknotesModel = $this->getBanknotesModel($id);
        
        if (!$this->canShiftActionCourier($shiftsCourierModel)) {
            throw new ForbiddenHttpException();
        }
                
        return $this->render('shiftCourierCheckSummary', [
            'shiftsCourierModel' => $shiftsCourierModel,
            'banknotesModel' => $banknotesModel, 
        ]);
    }
    
    
    public function actionShiftCloseFill()
    {
        if (!$this->canShiftAction()) {
            throw new ForbiddenHttpException();
        }
        
        $model = $this->getShift();
        $model->scenario = Shifts::SCENARIO_FILL;
        
        if (
            $model->load(Yii::$app->request->post()) &&
            $model->save()
        ) {
            return $this->redirect(['shift-close-summary']);
        }
        
        return $this->render('shiftCloseFill', [
            'model' => $model,
        ]);
    }
    
    public function actionShiftCloseSummary()
    {
        if (!$this->canShiftAction()) {
            throw new ForbiddenHttpException();
        }
        // Перед тем как закрывать суточную смену 
        // смена самовывоза должна быть закрыта.
        if (!$this->findShiftsCourierPickupModel()->isClosed) {
            throw new ForbiddenHttpException();
        }
        
        
        $searchModel = new ShiftsCourierSummarySearch();
        $dataProvider = $searchModel->search(
            $this->getShiftId(),
            Yii::$app->request->queryParams
        );
        
        $shiftsModel = $this->getShift();
        $shiftsModel->scenario = Shifts::SCEARIO_CLOSE;
        if ($shiftsModel->load(Yii::$app->request->post())) {
            try {
                Shifts::closeShift($shiftsModel);
                return $this->redirect(['shift-closed', 'id' => $shiftsModel->id]);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
           
        return $this->render('shiftCloseSummary', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'shiftsModel' => $shiftsModel,
        ]);
    }
    
    /**
     * Финальная страница, когда суточная смена уже закрыта.
     */
    public function actionShiftClosed($id)
    {
        $shift = $this->findShiftsModel($id);
        if (!$this->canShiftClosed($shift)) {
            throw new ForbiddenHttpException();
        }
        
        return $this->render('shiftClosed');
    }
    
    // Вспомогательные функции.
    ////////////////////////////////////////////////////////////////////////////
    
    private function findShiftsModel($id) 
    {
        $model = Shifts::findOne(['id' => $id]);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');    
        }
    }
    
    private function findShiftsPickerModel($id)
    {
        $model = ShiftsPicker::findOne(['id' => $id]);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');    
        }
    }
    
    private function findShiftsCourierModel($id) 
    {
        $model = ShiftsCourier::findOne(['id' => $id]);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }    
    }
    
    private function findShiftsCourierPickupModel()
    {
        $shift = $this->getShift();
        if ($shift === null) {
            throw new NotFoundHttpException('The requested page does not exist.'); 
        }
        
        $model = $shift->getShiftCourierPickup();
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');  
        }
        
        return $model;
    }
       
    private function getBanknotesModel($shiftsCourierId) 
    {
        $model = Banknotes::findOne(['shifts_courier_id' => $shiftsCourierId]);
        if ($model !== null) {
            return $model;
        }
        
        $model = new Banknotes();
        $model->shifts_courier_id = $shiftsCourierId;
        if (!$model->save()) {
            throw new \Exception();
        }
        
        return $model;
    }
    
    
    private function takeCashFromCourier($shiftsCourierModel, $banknotesModel)
    {
        $shiftsCourierModel->cash = $banknotesModel->sum;
        
        if (!$shiftsCourierModel->isTypeCourierPickup) {
            $paymentOptions = OptionsVal::getPaymentOptionsValsByDepartmentId($this->getDepartmentId());
            $shiftsCourierModel->calcPayment(
                $paymentOptions['pay_day_courier']->val,
                $paymentOptions['pay_even_courier']->val,
                $paymentOptions['pay_dop_courier']->val,
                $paymentOptions['pay_trip']->val
            );
        }
            
        return $shiftsCourierModel->save();
    }
               
    private function getFreeCouriersList()
    {
        $busyCouriersIds = ShiftsCourier::find()
            ->select(['courier_id'])
            ->where(['shifts_id' => $this->getShiftId()])
            ->column();
    
        return PickerApi::getCouriersList($this->getDepartmentId(), $busyCouriersIds);
    }
        
   private function getPickersList($withCurrentPicker = true)
   {
       $query = ShiftsPicker::find()
            ->select(['picker_id'])
            ->where([
                'shifts_id' => $this->getShiftId(),
                'state' => ShiftsPicker::STATE_OPENED,
            ]);
       if (!$withCurrentPicker) {
           $query->andWhere(['<>', 'picker_id', $this->getPickerId()]);
       }
       
       $pickersIds = $query->column(); 
            
       return PickerApi::getPickersList($pickersIds);
   }
    
}
