<?php

use yii\db\Schema;
use yii\db\Migration;

class m160901_174959_createCategoryStationTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%orders_category_station}}', [
            'city_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'category_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'station_id' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
                
        $this->addPrimaryKey(
            'pk-orders_category_station-city_id-category_id', 
            '{{%orders_category_station}}', 
            ['city_id', 'category_id']
        );
    }

    public function down()
    {
        $this->dropTable('{{%orders_category_station}}');
    }
    
}
