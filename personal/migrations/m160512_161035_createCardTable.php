<?php

use yii\db\Schema;
use yii\db\Migration;

class m160512_161035_createCardTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%personal_card}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING.' NOT NULL',
            'post_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'depart_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'birthday' => Schema::TYPE_DATE.' NOT NULL',
            'rate' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'phone' => Schema::TYPE_STRING.' DEFAULT NULL',
            'address' => Schema::TYPE_STRING.' NOT NULL',
            'med_book' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE',
            'date_employment' => Schema::TYPE_DATE.' DEFAULT NULL',
            'date_obt_input' => Schema::TYPE_DATE.' DEFAULT NULL',
            'date_obt_first' => Schema::TYPE_DATE.' DEFAULT NULL',
            'student' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE',
            'docs_ok' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE',
            'state' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
        
        $this->createIndex(
            'index__personal_card__post_id', 
            '{{%personal_card}}', 
            'post_id'
        );
        
        $this->addForeignKey(
            'fk__personal_card__post_id', 
            '{{%personal_card}}', 
            'post_id',
            '{{%personal_settings_post}}', 
            'id'
        );  
    }

    public function down()
    {
        $this->dropTable('{{%personal_card}}');
    }
    
}
