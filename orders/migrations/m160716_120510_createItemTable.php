<?php

use yii\db\Schema;
use yii\db\Migration;

class m160716_120510_createItemTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%orders_item}}', [
            'id' => Schema::TYPE_PK,
            'order_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'product_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'quantity' => Schema::TYPE_INTEGER.' NOT NULL',
            'price' => Schema::TYPE_INTEGER.' NOT NULL',
            'total_price' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
        
        
        $this->createIndex(
            'index-orders_item-order_id', 
            '{{%orders_item}}', 
            'order_id'
        );
        
        $this->addForeignKey(
            'fk-orders_item-order_id', 
            '{{%orders_item}}', 
            'order_id',
            '{{%orders_orders}}', 
            'id'
        );  
    }

    public function down()
    {
        $this->dropTable('{{%orders_item}}');
    }
    
}
