<?php

use yii\db\Schema;
use yii\db\Migration;

class m160716_100408_createOrdersTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%orders_orders}}', [
            'id' => Schema::TYPE_PK,
            'recipient' => Schema::TYPE_STRING.' DEFAULT NULL',
            'order_num' => Schema::TYPE_STRING.' NOT NULL',
            'city_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'client_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'user_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'stage_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'payment_type' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'start_date' => Schema::TYPE_INTEGER.' NOT NULL',
            'update_date' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'end_date' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'phone' => Schema::TYPE_STRING.' NOT NULL',
            'alter_phone' => Schema::TYPE_STRING.' DEFAULT NULL',
            'address' => Schema::TYPE_TEXT,
            'address_json' => Schema::TYPE_TEXT,
            'items_count' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'tax' => Schema::TYPE_INTEGER.' DEFAULT 0',
            'total_price' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'total_pay' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'return_sum' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'person_num' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 1',
            'is_paid' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE',
            'is_deleted' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE',
            'is_postponed' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE',
            'delivery_date' => Schema::TYPE_DATE.' DEFAULT NULL',
            'delivery_time' => Schema::TYPE_TIME.' DEFAULT NULL',
            'comment' => Schema::TYPE_STRING.' DEFAULT NULL',           
        ], $tableOptions);
        
        $this->createIndex(
            'index-orders_orders-stage_id', 
            '{{%orders_orders}}', 
            'stage_id'
        );
        
        $this->addForeignKey(
            'fk-orders_orders-stage_id', 
            '{{%orders_orders}}', 
            'stage_id',
            '{{%orders_stage}}', 
            'id'
        );  
        
        $this->createIndex(
            'index-orders_orders-payment_type', 
            '{{%orders_orders}}', 
            'payment_type'
        );
        
        $this->addForeignKey(
            'fk-orders_orders-payment_type', 
            '{{%orders_orders}}', 
            'payment_type',
            '{{%orders_payment}}', 
            'id'
        );  
        
    }

    public function down()
    {
        $this->dropTable('{{%orders_orders}}');
    }
    
}
