<?php

use yii\db\Schema;
use yii\db\Migration;

class m160716_123453_createDeliveryInfoTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%orders_delivery_info}}', [
            'id' => Schema::TYPE_PK,
            'order_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'planned_delivery_date' => Schema::TYPE_DATE.' NOT NULL',
            'planned_delivery_time' => Schema::TYPE_TIME.' NOT NULL',
            'price' => Schema::TYPE_INTEGER.' DEFAULT NULL',
        ], $tableOptions);
        
        
        $this->createIndex(
            'index-orders_delivery_info-order_id', 
            '{{%orders_delivery_info}}', 
            'order_id'
        );
        
        $this->addForeignKey(
            'fk-orders_delivery_info-order_id', 
            '{{%orders_delivery_info}}', 
            'order_id',
            '{{%orders_orders}}', 
            'id'
        );  
    }

    public function down()
    {
        $this->dropTable('{{%orders_delivery_info}}');
    }
    
}
