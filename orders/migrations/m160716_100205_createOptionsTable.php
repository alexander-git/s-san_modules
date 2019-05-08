<?php

use yii\db\Schema;
use yii\db\Migration;

class m160716_100205_createOptionsTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%orders_options}}', [
            'id' => Schema::TYPE_STRING.' NOT NULL',
            'name' => Schema::TYPE_STRING.' NOT NULL',
        ], $tableOptions);
        
        $this->addPrimaryKey('order_options-pk', '{{%orders_options}}', ['id']);
    }

    public function down()
    {
        $this->dropTable('{{%orders_options}}');
    }
    
}
