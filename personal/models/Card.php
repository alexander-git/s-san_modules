<?php

namespace app\modules\personal\models;

use Yii;

use app\modules\personal\helpers\DateHelper;

/**
 * This is the model class for table "{{%personal_card}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $post_id
 * @property integer $depart_id
 * @property string $birthday
 * @property integer $rate
 * @property string $phone
 * @property string $address
 * @property integer $med_book
 * @property string $date_employment
 * @property string $date_obt_input
 * @property string $date_obt_first
 * @property integer $student
 * @property integer $docs_ok
 * @property integer $state
 *
 * @property PersonalSettingsPost $post
 * @property PersonalCardDocs[] $personalCardDocs
 * @property PersonalMedbook[] $personalMedbooks
 */
class Card extends \yii\db\ActiveRecord
{
    const STATE_WORKS = 0;
    const STATE_DISMISSED = 1;
    
    const DATE_FORMAT = 'd-m-Y';
    const DATE_FORMAT_YII = 'php:d-m-Y';
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%personal_card}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
            
            ['depart_id', 'required'],
            ['depart_id', 'integer'],
            
            ['post_id', 'required'],
            ['post_id', 'integer'],
            [
                'post_id', 
                'exist', 
                'skipOnError' => true, 
                'targetClass' => 
                SettingsPost::className(), 
                'targetAttribute' => ['post_id' => 'id']
            ],
            
            ['rate', 'integer'],
            
            ['phone', 'required'],
            ['phone', 'string', 'max' => 255],
           
            ['address', 'required'],
            ['address', 'string', 'max' => 255],
            
            ['med_book', 'required'],
            ['med_book', 'boolean'],
            ['med_book', 'default', 'value' => 0], 
            
            ['birthday', 'required'],
            ['birthday', 'date', 'format' => self::DATE_FORMAT_YII],
            
            ['date_employment', 'date', 'format' => self::DATE_FORMAT_YII],
            
            ['date_obt_input', 'date', 'format' => self::DATE_FORMAT_YII],
            
            ['date_obt_first', 'date', 'format' => self::DATE_FORMAT_YII],
            
            ['student', 'required'],
            ['student', 'boolean'],
            ['student', 'default', 'value' => 0],
           
            ['docs_ok', 'required'],
            ['docs_ok', 'boolean'],
            ['docs_ok', 'default', 'value' => 0],
                
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
            'name' => 'ФИО',
            'post_id' => 'Должность',
            'depart_id' => 'Департамент',
            'birthday' => 'Дата рождения',
            'rate' => 'Ставка',
            'phone' => 'Телефон',
            'address' => 'Адрес',
            'med_book' => 'Мед книжка',
            'date_employment' => 'Дата приёма на работу',
            'date_obt_input' => 'Дата прохождения входного интсруктажа',
            'date_obt_first' => 'Дата прохождения первичного интсруктажа',
            'student' => 'Учащийся',
            'docs_ok' => 'Документы в порядке',
            'state' => 'Состояние',
        ];
    }
        
    public static function getStatesArray()
    {
        return [
            self::STATE_WORKS => 'Работает',
            self::STATE_DISMISSED => 'Уволен',
        ];
    }
        
    public function getStateName()
    {
        return self::getStatesArray()[$this->state];
    }

    public function isWorks()
    {
        return $this->state === self::STATE_WORKS;
    }
    
    public function isDismissed()
    {
        return $this->state === self::STATE_DISMISSED;
    }
    
    public function getDepartmentName()
    {
        return PersonalApi::getDepartmentName($this->depart_id);
    }
    
    public function getPostName()
    {
        return $this->settingsPost->name;
    }
    
    public function beforeSave($insert) 
    {
        if (!empty($this->birthday)) { 
            $this->birthday = DateHelper::convertDateToDbFormat($this->birthday, self::DATE_FORMAT);
        }
        if (!empty($this->date_employment)) { 
            $this->date_employment = DateHelper::convertDateToDbFormat($this->date_employment, self::DATE_FORMAT);
        }
        if (!empty($this->date_obt_input)) { 
            $this->date_obt_input = DateHelper::convertDateToDbFormat($this->date_obt_input, self::DATE_FORMAT);
        }
        if (!empty($this->date_obt_first)) { 
            $this->date_obt_first = DateHelper::convertDateToDbFormat($this->date_obt_first, self::DATE_FORMAT);
        }
        
        return parent::beforeSave($insert);
    }
    
    public function afterFind()
    {
        if (!empty($this->birthday)) {
            $this->birthday = DateHelper::convertDateFromDbFormat($this->birthday, self::DATE_FORMAT);
        }
        if (!empty($this->date_employment)) {
            $this->date_employment = DateHelper::convertDateFromDbFormat($this->date_employment, self::DATE_FORMAT);
        }
        if (!empty($this->date_obt_input)) {
            $this->date_obt_input = DateHelper::convertDateFromDbFormat($this->date_obt_input, self::DATE_FORMAT);
        }
        if (!empty($this->date_obt_first)) {
            $this->date_obt_first = DateHelper::convertDateFromDbFormat($this->date_obt_first, self::DATE_FORMAT);
        }
        
        parent::afterFind();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettingsPost()
    {
        return $this->hasOne(SettingsPost::className(), ['id' => 'post_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCardDocs()
    {
        return $this->hasMany(CardDocs::className(), ['card_id' => 'id']);
    }
    
    public function getDocsList()
    {
        return $this->hasMany(DocsList::className(), ['id' => 'docs_id'])
            ->via('cardDocs');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedbook()
    {
        return $this->hasOne(Medbook::className(), ['card_id' => 'id']);
    }
    
    public static function createCard($card)
    {
        $transaction = Card::getDb()->beginTransaction();
        try { 
            if (!$card->save()) {
                $transaction->rollBack();
                return false;
            }
            
            $needMeedBook = (boolean) $card->med_book; 
            if ($needMeedBook) {
                $medBook = new Medbook();
                $medBook->card_id = $card->id;
                if (!$medBook->save()) {
                    $transacation->rollBack();
                    return false;
                }
            }
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function updateCard($card)
    {
        $transaction = Card::getDb()->beginTransaction();
        try {
            if (!$card->save()) {
                $transaction->rollBack();
                return false;
            }
            
           $needMeedBook = (boolean) $card->med_book; 
           $hasMedbook = $card->getMedbook()->one() !== null;  
           if ($needMeedBook &&  !$hasMedbook) {
                $medBook = new Medbook();
                $medBook->card_id = $card->id;
                if (!$medBook->save()) {
                $transaction->rollBack();
                    return false;
                }
            } else if (!$needMeedBook && $hasMedbook) {
                if (!$card->medbook->delete()) {
                    $transaction->rollBack();
                }
            }
            
            $transaction->commit();
            return true;
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function deleteCard($card, $documentsPath)
    {
        $transaction = Card::getDb()->beginTransaction();
        try {
            $meedbook = $card->getMedbook()->one();
            if ($meedbook !== null && !$meedbook->delete()) {
                $transaction->rollBack();
                return false;
            }
            
            $cardDocsList = $card->cardDocs;
            foreach ($cardDocsList as $cardDocs) {
                if (!CardDocs::deleteDoc($cardDocs, $documentsPath)) {
                    $transaction->rollBack();
                    return false;
                }
            }
            
            if (!$card->delete()) {
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
      
}
