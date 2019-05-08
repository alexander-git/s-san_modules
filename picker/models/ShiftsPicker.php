<?php

namespace app\modules\picker\models;

use Yii;

/**
 * This is the model class for table "{{%picker_shifts_picker}}".
 *
 * @property integer $id
 * @property integer $date_open
 * @property integer $date_close
 * @property integer $picker_id
 * @property integer $shifts_id
 * @property integer $state
 *
 * @property PickerShiftsCourier[] $pickerShiftsCouriers
 * @property PickerShifts $shifts
 */
class ShiftsPicker extends \yii\db\ActiveRecord
{
    const STATE_OPENED = 0;
    const STATE_CLOSED = 1;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%picker_shifts_picker}}';
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
            
            ['shifts_id', 'required'],
            ['shifts_id', 'integer'],
            [
                'shifts_id',
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Shifts::className(), 
                'targetAttribute' => ['shifts_id' => 'id'],
            ],
            
            ['state', 'required'],
            ['state', 'integer'],
            ['state', 'in', 'range' => array_keys(self::getStatesArray())],
        ];
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
            'picker_id' => 'Комплектовщик',
            'shifts_id' => 'Суточная смена',
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
        return parent::beforeDelete();
    }

    public static function getStatesArray()
    {
        return [
            self::STATE_OPENED => 'Открыта',
            self::STATE_CLOSED => 'Закрыта',
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
    
    public function getIsClosed() 
    {
        return $this->state === self::STATE_CLOSED;
    }
    
    public function open() 
    {
        $this->date_open = PickerApi::getCurrentTimestamp();
        $this->state = self::STATE_OPENED;
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
        return $this->hasMany(ShiftsCourier::className(), ['shifts_picker_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShift()
    {
        return $this->hasOne(Shifts::className(), ['id' => 'shifts_id']);
    }
        
    public static function openShiftPicker($departmentId, $pickerId)
    {
        $transaction = ShiftsPicker::getDb()->beginTransaction();
        try {
            $shift = Shifts::find()
                ->where(['depart_id' => $departmentId])
                ->andWhere(['in', 'state', [
                    Shifts::STATE_OPENED, 
                    Shifts::STATE_CHECKING_BY_MAIN_PICKER
                ]])
                ->one();
            
            // Открыта ли суточная смена.
            $isShiftAlreadyOpened = $shift !== null;
            
            if ($isShiftAlreadyOpened && $shift->isCheckingByMainPicker) {
                throw new \Exception('Открытие смены курьера сейчас невозможно.');
            }
            
            if (!$isShiftAlreadyOpened) {
                $shift = new Shifts();
                $shift->depart_id = $departmentId;
                $shift->picker_id = $pickerId;
                $shift->open();
                if (!$shift->save()) {
                    throw new \Exception('Не удалось создать суточную смену');
                }
            }
            
            $shiftPicker = new ShiftsPicker();
            $shiftPicker->picker_id = $pickerId;
            $shiftPicker->shifts_id = $shift->id;
            $shiftPicker->open();
            if (!$shiftPicker->save()) {
                throw new \Exception('Не удалось создать смену комплектовщика');
            }
            
            if(!$isShiftAlreadyOpened) {
                // Создадим смену самовыовза.
                $shiftsCourierPickup = new ShiftsCourier();
                $shiftsCourierPickup->scenario = ShiftsCourier::SCENARIO_OPEN_PICKUP;
                $shiftsCourierPickup->shifts_id = $shift->id;
                $shiftsCourierPickup->shifts_picker_id = $shiftPicker->id;
                $shiftsCourierPickup->openPickup();
                if (!$shiftsCourierPickup->save()) {
                    throw new \Exceptiton('Не удалось создать смену самовывоза');
                }
            }
            
            $transaction->commit();
            return $shiftPicker;
        } 
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
}
