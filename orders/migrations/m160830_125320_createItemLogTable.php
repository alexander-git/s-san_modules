<?php

use yii\db\Schema;
use yii\db\Migration;

class m160830_125320_createItemLogTable extends Migration
{
    
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%orders_item_log}}', [
            'id' => Schema::TYPE_PK,
            'order_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'product_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'state' => Schema::TYPE_INTEGER.' NOT NULL',
            'station' => Schema::TYPE_INTEGER.' NOT NULL',
            'number' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'date_start' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'date_preparation' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'date_complete' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'date_pick_start' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'date_added' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'date_end' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'date_canceled' => Schema::TYPE_INTEGER.' DEFAULT NULL',
        ], $tableOptions);
                
        $this->createIndex(
            'index-orders_item_log-order_id', 
            '{{%orders_item_log}}', 
            'order_id'
        );
        
        $this->addForeignKey(
            'fk-orders_item_log-order_id', 
            '{{%orders_item_log}}', 
            'order_id',
            '{{%orders_orders}}', 
            'id'
        );  
    }

    public function down()
    {
        $this->dropTable('{{%orders_item_log}}');
    }
    
}
