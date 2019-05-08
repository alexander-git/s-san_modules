<?php

namespace app\modules\orders\models;

use app\modules\orders\exceptions\CanNotBeDeletedException;

/**
 * This is the model class for table "{{%orders_payment}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $sort
 *
 * @property Order[] $orders
 */
class PaymentType extends \yii\db\ActiveRecord
{
    private static $paymentTypeIdsCache = [];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orders_payment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
            ['name', 'unique'],
            
            ['sort', 'required'],
            ['sort', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'sort' => 'Порядок',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['payment_type' => 'id']);
    }
    
    public static function deletePaymentType($paymentType)
    {        
        $transaction = static::getDb()->beginTransaction();
        try {
            if (count($paymentType->orders) > 0) {
                throw new CanNotBeDeletedException('Удаление невозможно. Есть заказы с таким типом оплаты.');
            }
             
            if ($paymentType->delete() === false) {
                $transaction->rollBack();
                return false;
            }
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function getCashToCourierPaymentTypeId()
    {
        return self::getCachedPaymentTypeId('Наличными курьеру');   
    }
    
    public static function getCardToCourierPaymentTypeId()
    {
        return self::getCachedPaymentTypeId('Картой курьеру');   
    }
    
    private static function getCachedPaymentTypeId($paymentTypeName)
    {
        if (!isset(self::$paymentTypeIdsCache[$paymentTypeName])) {
            self::$paymentTypeIdsCache[$paymentTypeName] = (int) static::find()
                ->where(['name' => $paymentTypeName])
                ->scalar();
        }
                
        return self::$paymentTypeIdsCache[$paymentTypeName];
    }
    
}
