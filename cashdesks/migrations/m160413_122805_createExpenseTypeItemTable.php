<?php

use yii\db\Schema;
use yii\db\Migration;

class m160413_122805_createExpenseTypeItemTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%cashdesks_expense_type_item}}', [
            'id' => Schema::TYPE_PK,
            'expense_type_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'value' => Schema::TYPE_STRING.' NOT NULL',
        ], $tableOptions);
        
        $this->createIndex(
            'index__cashdesks_expense_type_item__expense_type_id', 
            '{{%cashdesks_expense_type_item}}', 
            'expense_type_id'
        );
        
        $this->addForeignKey(
            'fk__cashdesks_expense_type_item__expense_type_id', 
            '{{%cashdesks_expense_type_item}}',  
            'expense_type_id', 
            '{{%cashdesks_expense_type}}', 
            'id'
        );  
    }
    
    public function down()
    {
        $this->dropTable('{{%cashdesks_expense_type_item}}');
    }
    
    
}
