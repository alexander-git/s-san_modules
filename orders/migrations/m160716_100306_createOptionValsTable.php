<?php

use yii\db\Schema;
use yii\db\Migration;

class m160716_100306_createOptionValsTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%orders_option_vals}}', [
            'option_id' => Schema::TYPE_STRING.' NOT NULL',
            'value' => Schema::TYPE_STRING.' NOT NULL',
            'city_id' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
        
        $this->addPrimaryKey(
            'option_id-city_id', 
            '{{%orders_option_vals}}', 
            ['option_id', 'city_id']
        );
        
        $this->createIndex(
            'index-orders_option_vals-option_id', 
            '{{%orders_option_vals}}', 
            'option_id'
        );
        
        $this->addForeignKey(
            'fk-orders_option_vals-option_id', 
            '{{%orders_option_vals}}', 
            'option_id',
            '{{%orders_options}}', 
            'id'
        );  
        
    }

    public function down()
    {
        $this->dropTable('{{%orders_option_vals}}');
    }
    
}
