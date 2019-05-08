<?php

use yii\db\Schema;
use yii\db\Migration;

class m160327_220108_createBanknotesTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%picker_banknotes}}', [
            'id' => Schema::TYPE_PK,
            'shifts_courier_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'count_5000' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'count_1000' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'count_500' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'count_100' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'count_50' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'rest' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
        ], $tableOptions);
        
        $this->createIndex(
            'index__picker_banknotes__shifts_courier_id', 
            '{{%picker_banknotes}}',  
            'shifts_courier_id'
        );
        $this->addForeignKey(
            'fk__picker_banknotes__shifts_courier_id', 
            '{{%picker_banknotes}}', 
            'shifts_courier_id', 
            '{{%picker_shifts_courier}}', 
            'id'
        );   
    }

    public function down()
    {
        $this->dropTable('{{%picker_banknotes}}');
    }
    
}
