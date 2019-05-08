<?php

use yii\db\Schema;
use yii\db\Migration;

class m160623_093932_createBonuscardTypeTable extends Migration
{
    
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%clients_bonuscard_type}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING.' NOT NULL',
            'discount' => Schema::TYPE_INTEGER.' NOT NULL',
            'bonusquan' => Schema::TYPE_INTEGER.' NOT NULL',
            'minmoney' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%clients_bonuscard_type}}');
    }
    
}
