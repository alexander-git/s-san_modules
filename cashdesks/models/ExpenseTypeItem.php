<?php

namespace app\modules\cashdesks\models;

use Yii;

/**
 * This is the model class for table "{{%cashdesks_expense_type_item}}".
 *
 * @property integer $id
 * @property integer $expense_type_id
 * @property string $value
 *
 * @property ExpenseType $expenseType
 */
class ExpenseTypeItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cashdesks_expense_type_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['expense_type_id', 'required'],
            ['expense_type_id', 'integer'],
            [
                'expense_type_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => ExpenseType::className(),
                'targetAttribute' => ['expense_type_id' => 'id'],
            ],
            
            ['value', 'required'],
            ['value', 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'expense_type_id' => 'Вид расхода',
            'value' => 'Значение',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpenseType()
    {
        return $this->hasOne(ExpenseType::className(), ['id' => 'expense_type_id']);
    }
}
