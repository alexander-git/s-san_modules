<?php

use yii\db\Schema;
use yii\db\Migration;

class m160413_114010_createExpenseTypeTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%cashdesks_expense_type}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING.' NOT NULL',
            'type' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
    }
    
    public function down()
    {
        $this->dropTable('{{%cashdesks_expense_type}}');
    }
    
}
