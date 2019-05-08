<?php

namespace app\modules\clients\models;

use yii\db\Expression;
use app\modules\clients\exceptions\CanNotBeDeletedException;

/**
 * This is the model class for table "{{%clients_address}}".
 *
 * @property integer $id
 * @property integer $cityId
 * @property string $street
 * @property string $home
 * @property integer $appart
 * @property integer $floor
 * @property string $code
 * @property integer $entrance
 * @property string $name
 * @property string $desc
 *
 * @property ClientsAddressClients[] $clientsAddressClients
 * @property ClientsClients[] $clients
 */
class Address extends \yii\db\ActiveRecord
{
    private $_ordersCount;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%clients_address}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['cityId', 'required'],
            ['cityId', 'integer'],
            
            ['street', 'required'],
            ['street', 'string', 'max' => 255],
            
            ['home', 'required'],
            ['home', 'string', 'max' => 255],
            
            ['appart', 'integer', 'min' => 0],
            ['appart', 'default', 'value' => 0],
            
            ['code', 'string', 'max' => 255],
            
            ['entrance', 'integer'],
            
            ['floor', 'integer'],
            
            ['name', 'string', 'max' => 255],
            
            ['desc', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cityId' => 'Город',
            'street' => 'Улица',
            'home' => 'Дом',
            'appart' => 'Квартира',
            'floor' => 'Этаж',
            'code' => 'Код',
            'entrance' => 'Подъезд',
            'name' => 'Название места',
            'desc' => 'Примечание',
            'ordersCount' => 'Количество заказов',
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
        $clientAddresses = $this->clientAddresses;
        foreach ($clientAddresses as $clientAddress) {
            $clientAddress->delete();
        }
   
        return parent::beforeDelete();
    }
    
    public function setOrdersCount($value)
    {
        $this->_ordersCount = $value;
    }
    
    public function getOrdersCount()
    {
        if ($this->_ordersCount === null) {
            $this->_ordersCount = (int) ClientAddress::find()
                ->select(new Expression('SUM(ordercount)'))
                ->where(['addressId' => $this->id])
                ->scalar();
        }
        
        return $this->_ordersCount;
    }
    
    public function getCityName()
    {
        return ClientsApi::getCityNameById($this->cityId);
    }
    
    public function getCompositeName()
    {
        $result = 'г. '.$this->cityName.' ул. '.$this->street.' д. '.$this->home;
        if (!empty($this->appart) && ($this->appart !== 0)) {
            $result .= ' кв. '.$this->appart;            
        }
        
        return $result;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientAddresses()
    {
        return $this->hasMany(ClientAddress::className(), ['addressId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClients()
    {
        return $this->hasMany(Clients::className(), ['id' => 'clientId'])
            ->viaTable('clientAddresses', ['addressId' => 'id']);
    }
    
    
    public static function createAddressForClient(
        $addressModel, 
        $clientAddressModel,
        $clientId
    ) {
        $transaction = static::getDb()->beginTransaction();
        try {
            if (!$addressModel->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $clientAddressModel->clientId = (int) $clientId;
            $clientAddressModel->addressId = $addressModel->id;
            
            if (!$clientAddressModel->save()) {
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
    
    public static function updateAddressForClient(
        $addressModel, 
        $clientAddressModel
    ) {
        $transaction = static::getDb()->beginTransaction();
        try {
            if (!$addressModel->save()) {
                $transaction->rollBack();
                return false;
            }
            
            if (!$clientAddressModel->save()) {
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
    
    public static function deleteAddressForClient($addressModel, $clientId)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $clientAddressModel = ClientAddress::findOne([
                'addressId' => $addressModel->id,
                'clientId' => $clientId,
            ]);
            
            if (!$clientAddressModel->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            $clientAddressModels = ClientAddress::findAll(['addressId' => $addressModel->id]);
            if (count($clientAddressModels) > 0) {
                // К этому адресу привязаны другие клиенты.
                $errorMessage = 'Адрес не может быть удалён, так как '
                    .'он привязан к другим клиентам.';
                throw new CanNotBeDeletedException($errorMessage);
            }
            
            if (!$addressModel->delete()) {
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
    
    public static function addAddress($params)
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $query = Address::find()
                ->where([
                    'cityId' => (int) $params['cityId'],
                    'street' => $params['street'],
                    'home' => $params['home'],
                ]);
            if (isset($params['appart'])) {
                $query->andFilterWhere(['appart' => $params['appart']]);
            }
            
            $address = $query->one();
            
            if ($address === null) {
                $address = new Address();
                $allAttributes = $address->attributes();
                $attributes = array_diff($allAttributes, ['id']);
                foreach ($attributes as $attribute) {
                    if (isset($params[$attribute])) {
                        $address->$attribute = $params[$attribute];
                    }
                }
                if (!$address->save()) {
                    $transaction->rollBack();
                    return false;    
                }
            }

            if (isset($params['clientId'])) {
                $clientId = (int) $params['clientId'];
                $addressId = $address->id;
                
                $clientAddress = ClientAddress::findOne([
                    'clientId' => $clientId,
                    'addressId' => $addressId,
                ]);
                if ($clientAddress === null) {
                    $clientAddress = new ClientAddress();
                    $clientAddress->clientId = $clientId;
                    $clientAddress->addressId = $addressId;
                    $clientAddress->ordercount = 1;
                } else {
                    ++$clientAddress->ordercount;
                }
                if (!$clientAddress->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }     
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw  $e;
        }
    }
    
    /*
    private static function findSameAddress($addressModel)
    {
        $query = static::find()->
            where([
                'cityId' => $addressModel->cityId,
                'street' => $addressModel->street,
                'home' => $addressModel->home,
            ]);
        
        if (empty($addressModel->appart)) {
            $query->andWhere(['appart' => 0]);
        } else {
            $query->andWhere(['appart' => $addressModel->appart]);
        }
            
        return $query->one();        
    }
    */
    
}
