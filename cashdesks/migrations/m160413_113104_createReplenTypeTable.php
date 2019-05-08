<?php

use yii\db\Schema;
use yii\db\Migration;

class m160413_113104_createReplenTypeTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%cashdesks_replen_type}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING.' NOT NULL',
        ], $tableOptions);
    }
    
    public function down()
    {
        $this->dropTable('{{%cashdesks_replen_type}}');
    }
    
}
