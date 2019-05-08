<?php

use yii\db\Schema;
use yii\db\Migration;

class m160512_155612_createDocsListTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%personal_docs_list}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING.' NOT NULL',
            'type' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%personal_docs_list}}');
    }
    
}
