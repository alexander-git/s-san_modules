<?php

use yii\db\Schema;
use yii\db\Migration;

class m160325_095158_createShiftsCourierTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%picker_shifts_courier}}', [
            'id' => Schema::TYPE_PK,
            'date_open' => Schema::TYPE_INTEGER.' NOT NULL',
            'date_close' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'shifts_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'shifts_picker_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'courier_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'courier_name' => Schema::TYPE_STRING. ' NOT NULL',
            'courier_phone' => Schema::TYPE_STRING.' DEFAULT NULL',
            'type_courier' => Schema::TYPE_INTEGER.' NOT NULL',
            'check_sum' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'check_nocash' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'count_order' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'count_trip' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'spend' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'gifts' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'payment' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'cash' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'message' => Schema::TYPE_STRING.' DEFAULT NULL',
            'state' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
        ], $tableOptions);
        
        $this->createIndex(
            'index__picker_shifts_courier__shifts_picker_id', 
            '{{%picker_shifts_courier}}',  
            'shifts_picker_id'
        );
        $this->addForeignKey(
            'fk__picker_shifts_courier__shifts_picker_id', 
            '{{%picker_shifts_courier}}', 
            'shifts_picker_id', 
            '{{%picker_shifts_picker}}', 
            'id'
        );   
        
        $this->createIndex(
            'index__picker_shifts_courier__shifts_id', 
            '{{%picker_shifts_courier}}', 
            'shifts_id'
        );
        $this->addForeignKey(
            'fk__picker_shifts_courier__shifts_id', 
            '{{%picker_shifts_courier}}', 
            'shifts_id', 
            '{{%picker_shifts}}', 
            'id'
        );       
    }

    public function down()
    {
        $this->dropTable('{{%picker_shifts_courier}}');
    }
    
}
