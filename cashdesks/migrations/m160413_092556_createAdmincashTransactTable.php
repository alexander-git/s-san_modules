<?php

use yii\db\Schema;
use yii\db\Migration;

class m160413_092556_createAdmincashTransactTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%cashdesks_admincash_transact}}', [
            'id' => Schema::TYPE_PK,
            'depart_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'type' => Schema::TYPE_INTEGER.' NOT NULL',
            'banknotes_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER.' NOT NULL',
            'date_end' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'date_edit' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'administrator_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'user_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'user_edit_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'state' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'desc' => Schema::TYPE_STRING.' DEFAULT NULL',
            'type_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'type_value' => Schema::TYPE_STRING.' DEFAULT NULL',
            
        ], $tableOptions);
        
            
        $this->createIndex(
            'index__cashdesks_admincash_transact__banknotes_id', 
            '{{%cashdesks_admincash_transact}}', 
            'banknotes_id'
        );
        
        $this->addForeignKey(
            'fk__cashdesks_admincash_transact__banknotes_id', 
            '{{%cashdesks_admincash_transact}}',  
            'banknotes_id', 
            '{{%cashdesks_banknotes}}', 
            'id'
        );  
    }
    
    public function down()
    {
        $this->dropTable('{{%cashdesks_admincash_transact}}');
    }
    
}
