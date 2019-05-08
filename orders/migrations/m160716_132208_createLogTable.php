<?php

use yii\db\Schema;
use yii\db\Migration;

class m160716_132208_createLogTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%orders_log}}', [
            'id' => Schema::TYPE_PK,
            'order_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'stage_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'comment' => Schema::TYPE_STRING.' DEFAULT NULL',
            'date' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
        
        $this->createIndex(
            'index-orders_log-order_id', 
            '{{%orders_log}}', 
            'order_id'
        );
        
        $this->addForeignKey(
            'fk-orders_log-order_id', 
            '{{%orders_log}}', 
            'order_id',
            '{{%orders_orders}}', 
            'id'
        );  
        
        $this->createIndex(
            'index-orders_log-stage_id', 
            '{{%orders_orders}}', 
            'stage_id'
        );
        
        $this->addForeignKey(
            'fk-orders_log-stage_id', 
            '{{%orders_log}}', 
            'stage_id',
            '{{%orders_stage}}', 
            'id'
        );  
        
    }

    public function down()
    {
        $this->dropTable('{{%orders_log}}');
    }
    
}
