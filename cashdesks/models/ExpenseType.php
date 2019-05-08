<?php

namespace app\modules\cashdesks\models;

use Yii;

/**
 * This is the model class for table "{{%cashdesks_expense_type}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 *
 * @property ExpenseTypeItem[] $expenseTypeItems
 */
class ExpenseType extends \yii\db\ActiveRecord
{
    const SCENARIO_UPDATE = 'update';
    
    const TYPE_SALARY = 0;
    const TYPE_BANK = 1;
    const TYPE_ACCDEP = 2;
    const TYPE_ACCTAB = 3;
    const TYPE_SUPPLIER = 4;
    const TYPE_TEXT = 5;
    const TYPE_ARRAY = 6;
    
    private static $expenseAccdepId = null;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cashdesks_expense_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
            
            ['type', 'required'],
            ['type', 'integer'],
            ['type', 'in', 'range' => array_keys(self::getTypesArray())],
            ['type', 'validateType'],
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
        $expenseTypesItems = $this->getExpenseTypeItems()->all();
        foreach ($expenseTypesItems as $expenseTypeItem) {
            $expenseTypeItem->delete();
        }
        return parent::beforeDelete();
    }
    
    public function validateType($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }
        
        $singleInstanceTypes = [ 
            self::TYPE_SALARY,
            self::TYPE_BANK,
            self::TYPE_ACCDEP,
            self::TYPE_ACCTAB,
            self::TYPE_SUPPLIER
        ];
        
        if (!in_array($this->type, $singleInstanceTypes)) {
            return;
        }
        $model = static::findOne(['type' => $this->type]);
        if ($model === null) {
            return;
        }
        
        if ($this->isNewRecord) {
            $this->addError($attribute, 'Может быть только одна запись такого типа.');
        } else {
            if ($model->id !== $this->id) { 
                // Это не обновление существующей записи.
                $this->addError($attribute, 'Может быть только одна запись такого типа.');        
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'type' => 'Тип',
        ];
    }
    
    public function scenarios() {
        $scenarios = parent::scenarios();
        
        $scenarios[self::SCENARIO_UPDATE] = [
            'name',
        ];
        
        return $scenarios;
    }
    
    public static function getTypesArray()
    {
        return [
            self::TYPE_SALARY => 'Зарплата',
            self::TYPE_BANK => 'Перевод в банк',
            self::TYPE_ACCDEP => 'Перевод в бухгалтерию',
            self::TYPE_ACCTAB => 'Выдача в подотчёт',
            self::TYPE_SUPPLIER => 'Оплата поставщику',
            self::TYPE_TEXT => 'Текстовое поле',
            self::TYPE_ARRAY => 'Список значений',
        ];
    }
    
    public function getTypeName() 
    {
        return static::getTypesArray()[$this->type];
    }
    
    public function getIsTypeSalary()
    {
        return $this->type === self::TYPE_SALARY;
    }
    
    public function getIsTypeBank()
    {
        return $this->type === self::TYPE_BANK;
    }
    
    public function getIsTypeAccdep()
    {
        return $this->type === self::TYPE_ACCDEP;
    }

    public function getIsTypeAcctab() 
    {
        return $this->type === self::TYPE_ACCTAB;
    }
    
    public function getIsTypeSupplier() 
    {
        return $this->type === self::TYPE_SUPPLIER;
    }
    
    public function getIsTypeText() 
    {
        return $this->type === self::TYPE_TEXT;
    }
    
    public function getIsTypeArray()
    {
        return $this->type === self::TYPE_ARRAY;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpenseTypeItems()
    {
        return $this->hasMany(ExpenseTypeItem::className(), ['expense_type_id' => 'id']);
    }
    
    public static function getExpenseTypeAccdepId()
    {
        if (self::$expenseAccdepId === null) {
            $value = static::find()
                ->select('id')
                ->where(['type' => self::TYPE_ACCDEP])
                ->scalar();
            if (!$value) {
                throw new \Excption('Тип расхода "перевод в бухгалтерию" отсутствует.');          
            }
            
            self::$expenseAccdepId = (int) $value;
        }
        
        return self::$expenseAccdepId;
    }
    
}
