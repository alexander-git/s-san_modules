<?php

use yii\db\Schema;
use yii\db\Migration;

class m160413_130110_createPickercashTransactTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%cashdesks_pickercash_transact}}', [
            'id' => Schema::TYPE_PK,
            'depart_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'type' => Schema::TYPE_INTEGER.' NOT NULL',
            'banknotes_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER.' NOT NULL',
            'date_end' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'picker_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'user_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'state' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'desc' => Schema::TYPE_STRING.' DEFAULT NULL', 
        ], $tableOptions);
        
            
        $this->createIndex(
            'index__cashdesks_pickercash_transact__banknotes_id', 
            '{{%cashdesks_pickercash_transact}}', 
            'banknotes_id'
        );
        
        $this->addForeignKey(
            'fk__cashdesks_pickercash_transact__banknotes_id', 
            '{{%cashdesks_pickercash_transact}}',  
            'banknotes_id', 
            '{{%cashdesks_banknotes}}', 
            'id'
        );  
    }
    
    public function down()
    {
        $this->dropTable('{{%cashdesks_pickercash_transact}}');
    }
    
}
