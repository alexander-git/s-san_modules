<?php

namespace app\modules\picker\models;

use Yii;

/**
 * This is the model class for table "{{%picker_shifts}}".
 *
 * @property integer $id
 * @property integer $date_open
 * @property integer $date_close
 * @property integer $picker_id
 * @property integer $administrator_id
 * @property integer $buhgalter_id
 * @property integer $depart_id
 * @property integer $prog_turn
 * @property integer $prog_turn_nocash
 * @property integer $prog_check_count
 * @property integer $turn_cashdesk
 * @property string $message
 * @property integer $state
 *
 * @property PickerShiftsCourier[] $pickerShiftsCouriers
 * @property PickerShiftsPicker[] $pickerShiftsPickers
 */
class Shifts extends \yii\db\ActiveRecord
{
    const SCENARIO_FILL = 'fill';
    const SCEARIO_CLOSE = 'close';
    
    const STATE_OPENED = 0;
    const STATE_CHECKING_BY_MAIN_PICKER = 1;
    const STATE_CLOSED = 2;
    const STATE_CHECKED_BY_ADMIN = 3;
    const STATE_CHECKED_BY_BUHGALTER  = 4;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%picker_shifts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['date_open', 'required'],
            ['date_open', 'integer'],
            
            ['date_close', 'integer'],
            
            ['picker_id', 'required'],
            ['picker_id', 'integer'],
            
            ['administrator_id', 'integer'],
    
            ['buhgalter_id', 'integer'],
            
            ['depart_id', 'required'],
            ['depart_id', 'integer'],
            
            ['prog_turn', 'integer'],
            ['prog_turn', 'default', 'value' => 0],
            
            ['prog_turn_nocash', 'integer'],
            ['prog_turn_nocash', 'default', 'value' => 0],
            
            ['prog_check_count', 'integer'],
            ['prog_check_count', 'default', 'value' => 0],
            
            ['turn_cashdesk', 'integer'],
            ['turn_cashdesk', 'default', 'value' => 0],
            
            ['message', 'string', 'max' => 255],
            
            ['state', 'required'],
            ['state', 'integer'],
            ['state', 'in', 'range' => array_keys(self::getStatesArray())],
        ];
    }
    
    public function scenarios() 
    {
        $scenarios = parent::scenarios();
        
        $scenarios[self::SCENARIO_FILL] = [
            'prog_turn',
            'prog_turn_nocash',
            'prog_check_count',
            'turn_cashdesk',            
        ];
        
        $scenarios[self::SCEARIO_CLOSE] = [
            'message',
            '!date_close',
            '!state',
        ];
        
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_open' => 'Открыта',
            'date_close' => 'Закрыта',
            'picker_id' => 'Главный комплектовщик',
            'administrator_id' => 'Администратор',
            'buhgalter_id' => 'Бухгалтер',
            'depart_id' => 'Департамент',
            'prog_turn' => 'Оборот(прог)',
            'prog_turn_nocash' => 'Безнал(прог)',
            'prog_check_count' => 'Количество чеков(прог)',
            'turn_cashdesk' => 'Оборот(касса)',
            'message' => 'Комментарий',
            'state' => 'Состояние',
        ];
    }
    
    public function transactions() 
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_DELETE,
        ];
    }
    
    public function beforeDelete() 
    {
        $shiftsCouriers = $this->getShiftsCouriers()->all();
        foreach ($shiftsCouriers as $shiftCourier) {
            $shiftCourier->delete();
        }
        $shiftsPickers = $this->getShiftsPickers()->all(); 
         foreach ($shiftsPickers as $shiftPicker) {
            $shiftPicker->delete();
        }
        return parent::beforeDelete();
    }
    
    public static function getStatesArray()
    {
        return [
            self::STATE_OPENED => 'Открыта',
            self::STATE_CHECKING_BY_MAIN_PICKER => 'Проверяется главным комплектовщиком',
            self::STATE_CLOSED => 'Закрыта комплектовщиком',
            self::STATE_CHECKED_BY_ADMIN => 'Проверена Администратором',
            self::STATE_CHECKED_BY_BUHGALTER => 'Проверена бухгалтером',
        ];
    }
    
    public function getStateName() 
    {
        return static::getStatesArray()[$this->state];
    }
    
    public function getIsOpened() 
    {
        return $this->state === self::STATE_OPENED;
    }
    
    public function getIsCheckingByMainPicker() 
    {
        return $this->state === self::STATE_CHECKING_BY_MAIN_PICKER;
    }
    
    public function getIsClosed() 
    {
        return $this->state === self::STATE_CLOSED;
    }
    
    public function open() 
    {
        $this->date_open = PickerApi::getCurrentTimestamp();
        $this->state = self::STATE_OPENED;
    }
    
    public function startCheckingByMainPicker() 
    {
        $this->state = self::STATE_CHECKING_BY_MAIN_PICKER;
    }
    
    public function close()
    {
        $this->date_close = PickerApi::getCurrentTimestamp();
        $this->state = self::STATE_CLOSED;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShiftsCouriers()
    {
        return $this->hasMany(ShiftsCourier::className(), ['shifts_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShiftsPickers()
    {
        return $this->hasMany(ShiftsPicker::className(), ['shifts_id' => 'id']);
    }
    
    public function getShiftCourierPickup()
    {
        return $this->getShiftsCouriers()
            ->andWhere(['type_courier' => ShiftsCourier::TYPE_COURIER_PICKUP])
            ->one();
    }
        

    public static function closeShift($shift)
    {
        if ($shift->isClosed) {
            throw new \LogicException('Суточная смена уже закрыта');   
        }
        
        $transaction = Shifts::getDb()->beginTransaction();
        try {
            $mainPickerId = $shift->picker_id;
            $mainPickerShift = ShiftsPicker::findOne([
                'shifts_id' => $shift->id,
                'picker_id' => $mainPickerId,
                'state' => ShiftsPicker::STATE_OPENED
            ]);
            
            if ($mainPickerShift === null) {
                throw new \Exception('Смена главного комплектовщика не найдена');
            }
            
            $mainPickerShift->close();
            if (!$mainPickerShift->save()) {
               throw new \Exception('Не удалось закрыть смену главного комплектовщика');      
            }
            
            $shift->close();
            if (!$shift->save()) {
                throw new \Exception('Не удалось закрыть суточную смену');
            }
            $transaction->commit();
            return $shift;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
}
