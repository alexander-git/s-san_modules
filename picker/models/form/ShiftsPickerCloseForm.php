<?php

namespace app\modules\picker\models\form;

use Yii;

use yii\base\Model;
use yii\base\InvalidCallException;
use app\modules\picker\models\ShiftsPicker;
use app\modules\picker\models\ShiftsCourier;


class ShiftsPickerCloseForm extends Model 
{
    public $pickerId;
    
    // Для того, чтобы работал метод Model->load(). Т.к. у формы
    // есть только поле pickerId и оно не обязательно. В виде closeFlag
    // нужно выводить c помощью <input type="hidden">
    public $closeFlag = 1;
    
    private $shiftsModel;
    private $shiftsPickerModel;
    private $shiftPickerToTransfer; 
    private $shiftsCourierOpenedCount; 
     
    public function __construct($shiftsModel, $shiftsPickerModel, $config = [])
    {
        $this->shiftsModel = $shiftsModel;
        $this->shiftsPickerModel = $shiftsPickerModel;
        parent::__construct($config);
    }
    
    public function rules() 
    {
        return [
            ['closeFlag', 'required'],
            ['pickerId', 'validatePickerId'],    
        ];
    }
    
    public function attributeLabels() {
        return [
            'pickerId' => 'Комплектовщик',
        ];
    }

    public function getShiftsCourierOpenedCount() 
    {
        if ($this->shiftsCourierOpenedCount === null) {
            $this->shiftsCourierOpenedCount = ShiftsCourier::find()
                ->where([
                    'shifts_id' => $this->shiftsPickerModel->shifts_id,
                    'shifts_picker_id' => $this->shiftsPickerModel->id,
                    'state' => ShiftsCourier::STATE_OPENED,
                ])
                ->andWhere(['<>', 'type_courier', ShiftsCourier::TYPE_COURIER_PICKUP])
                ->count();
        }
        return (int) $this->shiftsCourierOpenedCount;
        
    }
    
    public function validatePickerId()
    {
        if (!$this->hasErrors()) {
            $needShiftsCourierTransfer = $this->getShiftsCourierOpenedCount() > 0;
            
            if (!$needShiftsCourierTransfer) {
                // Указывать комплектовщика не нужно.
                return;
            }
            
            if ($this->pickerId === null && $needShiftsCourierTransfer)  {
                $this->addError('pickerId', 'Нужно указать комплектовщика для передачи');
            } 
            
            if ($this->pickerId === $this->currentPickerId) {
                $this->addError('pickerId', 'Комплектовщик не должен совпадать с текущим');
            }
            
            $shiftPickerToTransfer = $this->getShiftPickerToTransfer(); 
            if ($shiftPickerToTransfer === null) {
                $this->addError('pickerId', 'Этому комплектовщику нельзя передать смены курьеров');
            }
        }
    }
    
    
    public function close() 
    {
        if ($this->shiftsPickerModel->isClosed) {
            throw new InvalidCallException();
        }
        $transaction = ShiftsPicker::getDb()->beginTransaction();
        try {
            $needShiftsCourierTransfer = $this->getShiftsCourierOpenedCount() > 0;
            
            if ($needShiftsCourierTransfer) {
                $shiftPickerToTransfer = $this->getShiftPickerToTransfer();
                $shiftsCouriers = $this->getShifstCourierOpened();
                foreach ($shiftsCouriers as $shiftCourier) {
                    $shiftCourier->shifts_picker_id = $shiftPickerToTransfer->id;
                    if (!$shiftCourier->save()) {
                        throw new \Exception();
                    }
                    
                }
            }
               
            if ($this->shiftsModel->picker_id === $this->shiftsPickerModel->picker_id) 
            {
                // Комплектовщик для которого закрываем смену был главным.
                $shiftPickerToTransfer = $this->getShiftPickerToTransfer();
                if ($shiftPickerToTransfer !== null) {
                    // Если задан pickerId комплектовщика, которому нужно 
                    // передать открытые смены курьеров и смена комплектовщика  
                    // с таким id существует, то сделаем этого комалектовщика главным.
                    $this->shiftsModel->picker_id = $shiftPickerToTransfer->picker_id;
                } else {
                    // Инача выберем главного комплектовщика случайно.
                    $this->shiftsModel->picker_id = $this->getNewMainPickerId();
                }
                if (!$this->shiftsModel->save()) {
                    throw new \Exception();
                }
                
                // Передадим смену самовывоза новому главному комплектовщику.
                $shiftCourierPickup = $shiftsModel->getShiftCourierPickup(); 
                if ($shiftCourierPickup === null) {
                    throw new \LogicException('Смена самовывоза отсутсвует.');
                }
                $shiftCourierPickup->shifts_picker_id = $this->getShiftPickerIdForMainPicker();
                if (!$shiftCourierPickup->save()) {
                    throw new \Exception();
                }
            }
            $this->shiftsPickerModel->close();
            if (!$this->shiftsPickerModel->save()) {
                throw new \Exception(); 
            }
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    private function getShiftPickerToTransfer()
    {
        if ($this->shiftPickerToTransfer === null) {
            $this->shiftPickerToTransfer = ShiftsPicker::findOne([
                'shifts_id' => $this->shiftsPickerModel->shifts_id, 
                'picker_id' => $this->pickerId,
                'state' => ShiftsPicker::STATE_OPENED,
            ]);
        }
        
        return $this->shiftPickerToTransfer;
    }
    
    private function getShifstCourierOpened() 
    {
        return ShiftsCourier::find()
            ->where([
                'shifts_id' => $this->shiftsPickerModel->shifts_id,
                'shifts_picker_id' => $this->shiftsPickerModel->id,
                'state' => ShiftsCourier::STATE_OPENED,
            ])
            ->all();
    }
       
    private function getShiftPickerIdForMainPicker() 
    {
        $result = ShiftsPicker::find()
                ->select('id')
                ->where([
                    'shifts_id' => $this->shiftsPickerModel->shifts_id,
                    'picker_id' => $this->shiftsModel->picker_id,
                ])
                ->scalar();
        if ($result === null) {
            throw  new \Exception();
        }
        
        return $result;
    }
    
    private function getNewMainPickerId() 
    {
        $ids = ShiftsPicker::find()
            ->select('id')
            ->where([
                'shifts_id' => $this->shiftsPickerModel->shifts_id,
                'state' => ShiftsPicker::STATE_OPENED,
            ])
            ->andWhere(['<>', 'picker_id', $this->shiftsPickerModel->picker_id])
            ->column();
        return $ids[0];
    }
    
}