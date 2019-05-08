<?php

namespace app\modules\clients\models;

use Yii;

/**
 * This is the model class for table "{{%clients_address_clients}}".
 *
 * @property integer $clientId
 * @property integer $addressId
 * @property integer $ordercount
 *
 * @property ClientsAddress $address
 * @property ClientsClients $client
 */
class ClientAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%clients_address_clients}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['clientId', 'required'],
            ['clientId', 'integer'],
            [
                'clientId', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Client::className(), 
                'targetAttribute' => ['clientId' => 'id']
            ],
            [
                'clientId',
                'unique',
                'targetAttribute' => ['clientId', 'addressId'],
            ],
            
            ['addressId', 'required'],
            ['addressId', 'integer'],
            [
                'addressId', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Address::className(), 
                'targetAttribute' => ['addressId' => 'id']
            ],
            
            ['ordercount', 'required'],
            ['ordercount', 'integer'],
            ['ordercount', 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'clientId' => 'Клиент',
            'addressId' => 'Адресс',
            'ordercount' => 'Количество заказов',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Address::className(), ['id' => 'addressId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'clientId']);
    }
    
}
