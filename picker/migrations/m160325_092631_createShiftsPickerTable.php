<?php

use yii\db\Schema;
use yii\db\Migration;

class m160325_092631_createShiftsPickerTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%picker_shifts_picker}}', [
            'id' => Schema::TYPE_PK,
            'date_open' => Schema::TYPE_INTEGER.' NOT NULL',
            'date_close' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'picker_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'shifts_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'state' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
        ], $tableOptions);
        
        $this->createIndex(
            'index__picker_shifts_picker__shifts_id', 
            '{{%picker_shifts_picker}}', 
            'shifts_id'
        );
        $this->addForeignKey(
            'fk__picker_shifts_picker__shifts_id', 
            '{{%picker_shifts_picker}}',  
            'shifts_id', 
            '{{%picker_shifts}}', 
            'id'
        );       
    }

    public function down()
    {
        $this->dropTable('{{%picker_shifts_picker}}');
    }
    
}
