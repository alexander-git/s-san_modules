<?php

use yii\db\Schema;
use yii\db\Migration;

class m160623_151943_createAddressTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%clients_address}}', [
            'id' => Schema::TYPE_PK,
            'cityId' => Schema::TYPE_INTEGER.' NOT NULL',
            'street' => Schema::TYPE_STRING.' NOT NULL',
            'home' => Schema::TYPE_STRING.' NOT NULL',
            'appart' => Schema::TYPE_STRING.' DEFAULT NULL',
            'floor' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'code' => Schema::TYPE_STRING.' DEFAULT NULL',
            'entrance' => Schema::TYPE_STRING.' DEFAULT NULL',
            'name' => Schema::TYPE_STRING.' DEFAULT NULL',
            'desc' => Schema::TYPE_TEXT,
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%clients_address}}');
    }
    
}
