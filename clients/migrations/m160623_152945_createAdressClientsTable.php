<?php

use yii\db\Schema;
use yii\db\Migration;

class m160623_152945_createAdressClientsTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%clients_address_clients}}', [
            'clientId' => Schema::TYPE_INTEGER.' NOT NULL',
            'addressId' => Schema::TYPE_INTEGER.' NOT NULL',
            'ordercount' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
        
        $this->addPrimaryKey(
            'pk-clients_address_clients', 
            '{{%clients_address_clients}}', 
            ['clientId', 'addressId']
        );
        
        $this->createIndex(
            'index-clients_address_clients-clientId', 
            '{{%clients_address_clients}}',  
            'clientId'
        );
        $this->addForeignKey(
            'fk-clients_address_clients-clientId', 
            '{{%clients_address_clients}}', 
            'clientId', 
            '{{%clients_clients}}', 
            'id'
        );
        
        $this->createIndex(
            'index-clients_address_clients-addressId', 
            '{{%clients_address_clients}}',  
            'addressId'
        );
        $this->addForeignKey(
            'fk-clients_address_clients-addressId', 
            '{{%clients_address_clients}}', 
            'addressId', 
            '{{%clients_address}}', 
            'id'
        );
    }

    public function down()
    {
        $this->dropTable('{{%clients_address_clients}}');
    }
    
}
