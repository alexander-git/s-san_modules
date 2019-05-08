<?php

namespace app\modules\clients\models;

use Yii;
use yii\db\Expression;
use app\modules\clients\helpers\DateHelper;

/**
 * This is the model class for table "{{%clients_clients}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $fullname
 * @property string $birthday
 * @property string $login
 * @property string $email
 * @property string $password
 * @property string $phone
 * @property string $alterPhone
 * @property string $description
 * @property string $note
 * @property integer $cardnum
 * @property integer $state
 *
 * @property ClientAddresses[] $clientAddresses
 * @property Address[] $addresses
 * @property Bonuscard $bonuscard
 */

class Client extends \yii\db\ActiveRecord
{
    const STATE_NEW = 0;
    const STATE_PERMANENT = 1;
    const STATE_IN_BLACK_LIST = 2;
    
    const DATE_FORMAT = 'd-m-Y';
    const DATE_FORMAT_YII = 'php:d-m-Y';
    
    private $_ordersCount;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%clients_clients}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
            
            ['fullname', 'required'],
            ['fullname', 'string', 'max' => 255],
            
            ['login', 'required'],
            ['login', 'string', 'max' => 255],
            ['login', 'unique'],
            
            ['email', 'string', 'max' => 255],
            ['email', 'email'],
            ['email', 'unique'],
            
            ['password', 'required'],
            ['password', 'string', 'max' => 255],
            
            ['phone', 'required'],
            ['phone', 'string', 'max' => 255],
            ['phone', 'match', 'pattern' => '/^[0-9]+$/'],
            ['phone', 'unique'],
            
            ['alterPhone', 'string', 'max' => 255],
            ['alterPhone', 'match', 'pattern' => '/^[0-9]+$/'],
            
            ['birthday', 'required'],
            ['birthday', 'date', 'format' => self::DATE_FORMAT_YII],
            
            ['description', 'string'],
            ['note', 'string'],
                
            ['cardnum', 'integer'],
            [
                'cardnum', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => Bonuscard::className(), 
                'targetAttribute' => ['cardnum' => 'id']
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
            'name' => 'Имя',
            'fullname' => 'Полное имя',
            'birthday' => 'Дата рождения',
            'login' => 'Логин',
            'email' => 'Email',
            'password' => 'Пароль',
            'phone' => 'Телефон',
            'alterPhone' => 'Дополнительный телефон',
            'description' => 'Описание клиента',
            'note' => 'Примечание',
            'cardnum' => 'Бонусная карта',
            'state' => 'Состояние',
            'ordersCount' => 'Количество заказов',
        ];
    }
    
    public function transactions() 
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_DELETE,
        ];
    }
    
    public static function getStatesArray() 
    {
        return [
            self::STATE_NEW => 'Новый',
            self::STATE_PERMANENT => 'Постоянный',
            self::STATE_IN_BLACK_LIST => 'В чёрном списке', 
        ];
    }
    
    public function getStateName()
    {
        return static::getStatesArray()[$this->state];
    }
    
    public function getIsNew()
    {
        return $this->state === self::STATE_NEW;
    }
    
    public function getIsPermanent()
    {
        return $this->state === self::STATE_PERMANENT;
    }
    
    public function getIsInBlackList()
    {
        return $this->state === self::STATE_IN_BLACK_LIST;
    }
    
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }
    
    public function validatePassword($password)        
    {
        return Yii::$app->security->validatePassword($password, $this->password);
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
                ->where(['clientId' => $this->id])
                ->scalar();
        }
        
        return $this->_ordersCount;
    }

    public function beforeSave($insert) 
    {
        if (!empty($this->birthday)) { 
            $this->birthday = DateHelper::convertDateToDbFormat($this->birthday, self::DATE_FORMAT);
        }        
        
        return parent::beforeSave($insert);
    }
    
    public function afterFind()
    {
        if (!empty($this->birthday)) {
            $this->birthday = DateHelper::convertDateFromDbFormat($this->birthday, self::DATE_FORMAT);
        }
    
        parent::afterFind();
    }
    
    public function beforeDelete() 
    {
        $clientAddresses = $this->clientAddresses;
        foreach ($clientAddresses as $clientAddress) {
            $clientAddress->delete();
        }
        
        $bonuscard = $this->bonuscard;
        if ($bonuscard !== null) {
            $bonuscard->delete();
        }
        
        return parent::beforeDelete();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientAddresses()
    {
        return $this->hasMany(ClientAddress::className(), ['clientId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany(Address::className(), ['id' => 'addressId'])
            ->viaTable('clientAddresses', ['clientId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBonuscard()
    {
        return $this->hasOne(Bonuscard::className(), ['id' => 'cardnum']);
    }
    
}
