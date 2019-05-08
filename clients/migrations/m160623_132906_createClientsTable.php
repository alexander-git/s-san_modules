<?php

use yii\db\Schema;
use yii\db\Migration;

class m160623_132906_createClientsTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%clients_clients}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING.' NOT NULL',
            'fullname' => Schema::TYPE_STRING.' NOT NULL',
            'birthday' => Schema::TYPE_DATE.' DEFAULT NULL',
            'login' => Schema::TYPE_STRING.' NOT NULL',
            'email' => Schema::TYPE_STRING.' DEFAULT NULL',
            'password' => Schema::TYPE_STRING.' NOT NULL',
            'phone' => Schema::TYPE_STRING.' DEFAULT NULL',
            'alterPhone' => Schema::TYPE_STRING.' DEFAULT NULL',
            'description' => Schema::TYPE_TEXT,
            'note' => Schema::TYPE_TEXT,
            'cardnum' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'state' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
        ], $tableOptions);
        
        $this->createIndex(
            'index-clients_clients-cardnum', 
            '{{%clients_clients}}',  
            'cardnum'
        );
        $this->addForeignKey(
            'fk-clients_clients-cardnum', 
            '{{%clients_clients}}', 
            'cardnum', 
            '{{%clients_bonuscard}}', 
            'id'
        ); 
    }

    public function down()
    {
        $this->dropTable('{{%clients_clients}}');
    }
    
}
