<?php

namespace app\modules\orders\models;

use Yii;

/**
 * This is the model class for table "{{%orders_item}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $product_id
 * @property integer $quantity
 * @property integer $price
 * @property integer $total_price
 *
 * @property Order $order
 */
class OrderItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orders_item}}';
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
            
            ['product_id', 'required'],
            ['product_id', 'integer'],
            
            ['quantity', 'required'],
            ['quantity', 'integer'],
            
            ['price', 'required'],
            ['price', 'integer'],
            
            ['total_price', 'required'],
            ['total_price', 'integer'],
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
            'product_id' => 'Продукт',
            'quantity' => 'Количество',
            'price' => 'Цена',
            'total_price' => 'Общая цена',
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
