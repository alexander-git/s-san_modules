<?php

namespace app\modules\picker\filters;

use yii\base\ActionFilter;
use app\modules\picker\models\Shifts;
use app\modules\picker\models\ShiftsPicker;
use app\modules\picker\models\ShiftsCourier;
use app\modules\picker\models\PickerApi;

class PickerWorkPrepareFilter extends ActionFilter 
{
    /**
     * ID текщуго комплектовщика.
     * @var integer 
     */
    private $pickerId;
    
    /**
     * ID департамента текщуго комплектовщика.
     * @var integer 
     */
    private $departmentId;
    
    
    /**
     * Текущая суточная смена.
     * @var \app\modules\picker\models\Shifts 
     */
    private $shift;
    
    /**
     * Смена комплектовщика.
     * @var \app\modules\picker\models\ShiftsPicker 
     */
    private $shiftPicker;
    
    
    public function beforeAction($action) 
    {
        $this->pickerId = PickerApi::getCurrentUserId();
        $this->departmentId = PickerApi::getCurrentUserDepartmentId(); 
        
        // Найдём суточную смену.
        $this->shift = Shifts::find()
            ->where(['depart_id' => $this->departmentId])
            ->andWhere(['in', 'state', [
                Shifts::STATE_OPENED, 
                Shifts::STATE_CHECKING_BY_MAIN_PICKER
            ]])
            ->one();

        // Найдём смену комплектовщика.
        if ($this->shift !== null) {
            $this->shiftPicker = ShiftsPicker::findOne([
                'shifts_id' => $this->shift->id,
                'picker_id' => $this->pickerId,
            ]);  
        }
        
        return true;
    }
    
    public function getPickerId() 
    {
        return $this->pickerId;
    }
    
    public function getDepartmentId()
    {
        return $this->departmentId;
    }
    
    public function getShiftId()
    {
        if ($this->shift === null) {
            return null;
        }
        return $this->shift->id;
    }
    
    public function getShiftPickerId() 
    {
        if ($this->shiftPicker === null) {
            return null;
        }
        return $this->shiftPicker->id;
        
    }
    
    public function getMainPickerId()
    {
        if ($this->shift === null) {
            return null;
        }
        return $this->shift->picker_id;
    }
    
    public function getShift() 
    {
        return $this->shift;
    }
    
    public function setShift($value) 
    {
        $this->shift = $value;
    }
    
    public function getShiftPicker()
    {
        return $this->shiftPicker;
    }
    
    public function setShiftPicker($value)
    {
        $this->shiftPicker = $value;
    }
     
    public function isShiftOpened() 
    {
        return ($this->shift !== null) && $this->shift->isOpened;
    }
    
    public function isShiftCheckingByMainPicker()
    {
        return ($this->shift !== null) && $this->shift->isCheckingByMainPicker;
    }
    
    public function isShiftPickerExist()
    {
        return $this->shiftPicker !==  null;
    }
    
    public function isShiftPickerOpened() 
    {
        return ($this->shiftPicker !== null) && $this->shiftPicker->isOpened;
    }

    public function isShiftPickerClosed() 
    {
        return ($this->shiftPicker !== null) && $this->shiftPicker->isClosed;
    }
    
    public function isMainPicker() 
    {
        if ($this->shift === null) {
            return false;
        }
        return $this->getPickerId() === $this->shift->picker_id;
    }
    
    public function isLastPicker() 
    {
        return $this->getShiftsPickerOpenedCount() === 1;
    }
    
    /**
     * @param boolean $withShiftCourierPickup Учитывать ли смену самовывоза.
     * @return int Количесвто открытых смен курьеров для текущего комплектовщика.
     */
    public function getShiftsCourierOpenedCount($withShiftCourierPickup = false)
    {
        
        $query = ShiftsCourier::find()
            ->where([
                'shifts_id' => $this->getShiftId(),
                'shifts_picker_id' => $this->getShiftPickerId(),
                'state' => ShiftsCourier::STATE_OPENED,
            ]);
            
        if (!$withShiftCourierPickup) {
            $query->andWhere(['<>', 'type_courier', ShiftsCourier::TYPE_COURIER_PICKUP]); 
        }
                
        return  (int) $query->count();
    }
    
    public function getAccsessInfo()
    {
        $result = [
            'pickerId' => $this->getPickerId(),
            'shiftId' => $this->getShiftId(),
            'shiftPickerId' => $this->getShiftPickerId(),
        ];
        
        $shift = $this->getShift();
        if ($shift !== null) {
            $result['shift'] = $shift;
        }
        
        $shiftPicker = $this->getShiftPicker();
        if ($shift !== null) {
            $result['shiftPicker'] = $shiftPicker;
        }
        
        return $result;
    }
               
    // Проверка доступа к различным действиям ViewController.
    ////////////////////////////////////////////////////////////////////////////
    public function canShiftPickerIndex()
    {
        if ($this->isShiftPickerOpened()) {
            return false;
        }
        
        return true;
    }
    
    
    public function canShiftPickerOpen()
    {
        if ($this->isShiftCheckingByMainPicker()) {
            return false;
        }
        
        // Если смена открыта или уже была закрыта, то закрывть её нельзя.
        if ($this->isShiftPickerExist()) {
            return false;
        }

        return true;
    }
    
    /**
     * @param \app\modules\picker\models\ShiftsPicker $shiftPicker
     * @return boolean
     */
    public function canShiftPickerClosed($shiftPicker) 
    {
        if (!$shiftPicker->isClosed) {
            return false;
        }
        
        if ($shiftPicker->picker_id !== $this->getPickerId()) {
            return false;
        }
        
        return true;  
    }
    
    // Доступ к различным действиям внутри смены комплектовщика.
    ////////////////////////////////////////////////////////////////////////////
    
    /**
     * Общая проверка для всех действий в рамках открытой смены комплектовщика.
     * @return boolean
     */
    public function canShiftPickerAction()
    {
        if (!$this->isShiftOpened()) {
            return false;
        }
        
        return true;
    }
      
    public function canShiftPickerClose()
    {
        if (!$this->canShiftPickerAction()) {
            return false;
        }
        
        if ($this->isLastPicker()) {
            return false;
        }
        
        return true;
    }
        
    /**
     * Проверка может ли комплектовщик как-либо изменять смену курьера пока
     * работает со своей сменой.
     * @param \app\modules\picker\models\ShiftsCourier $shiftCourier
     * @return boolean
     */
    public function canShiftPickerActionCourier($shiftCourier) 
    {
        if (!$this->canShiftPickerAction()) {
            return false;
        }
        if ($shiftCourier->shifts_id !== $this->getShiftId()) {
            return false;
        }
        if ($shiftCourier->shifts_picker_id !== $this->getShiftPickerId()) {
            return false;
        }
        
        return true;
    }
    
    
    public function canShiftCheckingPreview()
    {
        if (!$this->canShiftPickerAction()) {
            return false;
        }
        if (!$this->isLastPicker()) {
            return false;
        }
        if (!$this->isMainPicker()) {
            return false;
        }
        
        return true;
    }
    
    public function canShiftCheckingByMainPickerStart()
    {
        if (!$this->canShiftPickerAction()) {
            return false;
        }
        if (!$this->isLastPicker()) {
            return false;
        }
        if (!$this->isMainPicker()) {
            return false;
        }
        
        if ($this->getShiftsCourierOpenedCount() !== 0) {
            return false;
        }
        
        return true;
    }
       
    // Доступ к различным действиям внутри смены комплектовщика.
    ////////////////////////////////////////////////////////////////////////////
    
    /**
     * Общая проверка для всех действий происходящих в процессе закрытия 
     * суточной смены.
     * @return boolean
     */
    public function canShiftAction() 
    {
        if (!$this->isShiftPickerOpened()) {
            return false;
        }
        if (!$this->isShiftCheckingByMainPicker()) {
            return false;
        }
        if (!$this->isMainPicker()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Проверка может ли комплектовщик как-либо изменять смену курьера, когда
     * происходит закрытие суточной смены.
     * @param \app\modules\picker\models\ShiftsCourier $shiftCourier
     * @return boolean
     */
    public function canShiftActionCourier($shiftCourier)
    {
        if (!$this->canShiftAction()) {
           return false;
        }
        
        if ($shiftCourier->shifts_id !== $this->getShiftId()) {
            return false;
        }
        
        return true;
    }
 
    public function canShiftClosed($shift) 
    {
        if (!$shift->isClosed) {
            return false;
        }
        
        if ($shift->picker_id !== $this->getPickerId()) {
            return false;
        }
        
        return true;  
    }
      
    // Вспоомгательные функции.
    ////////////////////////////////////////////////////////////////////////////
    
    private function getShiftsPickerOpenedCount()
    {
        $shiftsPickerOpenedCount = (int) ShiftsPicker::find()
            ->where([
                'shifts_id' => $this->getShiftId(), 
                'state' => ShiftsPicker::STATE_OPENED
            ])
            ->count();
        
        return $shiftsPickerOpenedCount;
    }
        
}