<?php

use yii\db\Schema;
use yii\db\Migration;

class m160512_163558_createCardDocsTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%personal_card_docs}}', [
            'card_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'docs_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'check' => Schema::TYPE_BOOLEAN.' NOT NULL',
            'file' => Schema::TYPE_STRING.' DEFAULT NULL',
        ], $tableOptions);
        
        $this->addPrimaryKey('id', '{{%personal_card_docs}}', ['card_id', 'docs_id']);
        
        $this->createIndex(
            'index__personal_card_docs__card_id', 
            '{{%personal_card_docs}}', 
            'card_id'
        );
        
        $this->addForeignKey(
            'fk__personal_card_docs__card_id', 
            '{{%personal_card_docs}}', 
            'card_id',
            '{{%personal_card}}', 
            'id'
        );
        
        $this->createIndex(
            'index__personal_card_docs__docs_id', 
            '{{%personal_card_docs}}', 
            'docs_id'
        );
        
        $this->addForeignKey(
            'fk__personal_card_docs__docs_id', 
            '{{%personal_card_docs}}', 
            'docs_id',
            '{{%personal_docs_list}}', 
            'id'
        );
    }

    public function down()
    {
        $this->dropTable('{{%personal_card_docs}}');
    }
    
}
