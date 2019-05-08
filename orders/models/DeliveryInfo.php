<?php

namespace app\modules\orders\models;


use app\modules\orders\helpers\DateHelper;
use app\modules\orders\helpers\TimeHelper;
use app\modules\orders\models\DateTimeConstsInterface;

/**
 * This is the model class for table "{{%orders_delivery_info}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $planned_delivery_date
 * @property string $planned_delivery_time
 * @property integer $price
 *
 * @property Order $order
 */
class DeliveryInfo extends \yii\db\ActiveRecord implements DateTimeConstsInterface
{   
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orders_delivery_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['order_id', 'required'],
            ['order_id', 'integer'],
            [
                'order_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Order::className(), 
                'targetAttribute' => ['order_id' => 'id']
            ],
            
            ['planned_delivery_date', 'required'],
            ['planned_delivery_date', 'date', 'format' => self::DATE_FORMAT_YII],
            
            ['planned_delivery_time', 'required'],
            ['planned_delivery_time', 'date', 'format' => self::TIME_SHORT_FORMAT_YII],
        
            ['price', 'integer'],        
        ];
    }

    public function beforeSave($insert) 
    {
        if (!empty($this->planned_delivery_date)) { 
            $this->planned_delivery_date = DateHelper::convertDateToDbFormat($this->planned_delivery_date, self::DATE_FORMAT);
        }
        
        if (!empty($this->planned_delivery_time)) { 
            $this->planned_delivery_time = TimeHelper::convertTimeToDbFormat($this->planned_delivery_time, self::TIME_SHORT_FORMAT);
        }

        return parent::beforeSave($insert);
    }
    
    public function afterFind()
    {
        if (!empty($this->planned_delivery_date)) {
            $this->planned_delivery_date = DateHelper::convertDateFromDbFormat($this->planned_delivery_date, self::DATE_FORMAT);
        }
        
        if (!empty($this->planned_delivery_time)) {
            $this->planned_delivery_time = TimeHelper::convertTimeFromDbFormat($this->planned_delivery_time, self::TIME_SHORT_FORMAT);
        }
        
        parent::afterFind();
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Заказ',
            'planned_delivery_date' => 'Планируемая дата доставки',
            'planned_delivery_time' => 'Планируемое время доставки',
            'price' => 'Цена',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
    
}
