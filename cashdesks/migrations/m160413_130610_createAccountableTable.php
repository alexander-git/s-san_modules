<?php

use yii\db\Schema;
use yii\db\Migration;

class m160413_130610_createAccountableTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%cashdesks_accountable}}', [
            'depart_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'current' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'max_sum' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
        ], $tableOptions);
        
            
        $this->addPrimaryKey(
            'index__cashdesks_accountable__depart_id', 
            '{{%cashdesks_accountable}}', 
            'depart_id'
        );
    }
    
    public function down()
    {
        $this->dropTable('{{%cashdesks_accountable}}');
    }
    
}
