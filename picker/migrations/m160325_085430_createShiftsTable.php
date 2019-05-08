<?php

use yii\db\Schema;
use yii\db\Migration;

class m160325_085430_createShiftsTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%picker_shifts}}', [
            'id' => Schema::TYPE_PK,
            'date_open' => Schema::TYPE_INTEGER.' NOT NULL',
            'date_close' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'picker_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'administrator_id' => Schema::TYPE_INTEGER,
            'buhgalter_id' => Schema::TYPE_INTEGER,
            'depart_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'prog_turn' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'prog_turn_nocash' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'prog_check_count' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'turn_cashdesk' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'message' => Schema::TYPE_STRING.' DEFAULT NULL',
            'state' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
        ], $tableOptions);
        
        $this->createIndex('index__picker_shifts__depart_id', '{{%picker_shifts}}', 'depart_id');
        
    }

    public function down()
    {
        $this->dropTable('{{%picker_shifts}}');
    }
    
}
