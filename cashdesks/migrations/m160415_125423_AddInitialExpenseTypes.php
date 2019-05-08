<?php

use yii\db\Schema;
use yii\db\Migration;
use app\modules\cashdesks\models\ExpenseType;

class m160415_125423_AddInitialExpenseTypes extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('{{%cashdesks_expense_type}}', ['name', 'type'], [
            ['Заработная плата', ExpenseType::TYPE_SALARY],
            ['Перевод в банк', ExpenseType::TYPE_BANK],
            ['Перевод в бухгалтерию', ExpenseType::TYPE_ACCDEP],
            ['Выдача в подотчёт', ExpenseType::TYPE_ACCTAB],
            ['Оплата поставщику', ExpenseType::TYPE_SUPPLIER],
        ]);
    }

    public function safeDown()
    {   
        $this->delete('{{%cashdesks_expense_type}}', [
            'name' => [
                'Заработная плата', 
                'Перевод в банк', 
                'Перевод в бухгалтерию',
                'Выдача в подотчёт',
                'Оплата поставщику',
            ],
        ]);        
    }
    
}
