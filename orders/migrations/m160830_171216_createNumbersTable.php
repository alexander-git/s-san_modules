<?php

use yii\db\Schema;
use yii\db\Migration;

class m160830_171216_createNumbersTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%orders_number}}', [
            'number' => Schema::TYPE_INTEGER.' NOT NULL',
            'city_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'station_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'order_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'free' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT TRUE',
            'date' => Schema::TYPE_INTEGER.' DEFAULT NULL',
        ], $tableOptions);
        
        
        $this->addPrimaryKey(
            'pk-orders_number', 
            '{{%orders_number}}', 
            ['number', 'city_id', 'station_id']
        );
    }

    public function down()
    {
        $this->dropTable('{{%orders_number}}');
    }
}
