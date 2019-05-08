<?php

use yii\db\Schema;
use yii\db\Migration;

class m160413_131757_createAccountableTransactTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%cashdesks_accountable_transact}}', [
            'id' => Schema::TYPE_PK,
            'depart_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'type' => Schema::TYPE_INTEGER.' NOT NULL',
            'sum' => Schema::TYPE_INTEGER.' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER.' NOT NULL',
            'picker_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'user_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'desc' => Schema::TYPE_STRING.' DEFAULT NULL', 
        ], $tableOptions);
        
            
        $this->createIndex(
            'index__cashdesks_accountable_transact__depart_id', 
            '{{%cashdesks_accountable_transact}}', 
            'depart_id'
        );
        
        $this->addForeignKey(
            'fk__cashdesks_accountable_transact__depart_id', 
            '{{%cashdesks_accountable_transact}}',  
            'depart_id', 
            '{{%cashdesks_accountable}}', 
            'depart_id'
        );  
    }
    
    public function down()
    {
        $this->dropTable('{{%cashdesks_accountable_transact}}');
    }
    
}
