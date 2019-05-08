<?php

namespace app\modules\orders\models;

/**
 * This is the model class for table "{{%orders_log}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $stage_id
 * @property string $comment
 * @property integer $date
 *
 * @property OrdersOrders $order
 * @property OrdersStage $stage
 */
class LogRecord extends \yii\db\ActiveRecord
{
    const SCENARIO_COMMENT_REQUIRED = 'commentRequired';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orders_log}}';
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
            
            ['stage_id', 'required'],
            ['stage_id', 'integer'],
            [
                'stage_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Stage::className(), 
                'targetAttribute' => ['stage_id' => 'id']
            ],
            
            ['comment', 'required', 'on' => [self::SCENARIO_COMMENT_REQUIRED]],
            ['comment', 'string', 'max' => 255],
            
            ['date', 'required'],
            ['date', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Заказ',
            'stage_id' => 'Стадия',
            'comment' => 'Комментарий',
            'date' => 'Дата',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStage()
    {
        return $this->hasOne(Stage::className(), ['id' => 'stage_id']);
    }
    
}
