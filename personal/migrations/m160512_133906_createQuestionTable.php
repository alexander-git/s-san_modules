<?php

use yii\db\Schema;
use yii\db\Migration;

class m160512_133906_createQuestionTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%personal_question}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING.' NOT NULL',
            'post_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'birthday' => Schema::TYPE_DATE.' DEFAULT NULL',
            'city' => Schema::TYPE_STRING.' DEFAULT NULL',
            'address' => Schema::TYPE_STRING.' DEFAULT NULL',
            'phone' => Schema::TYPE_STRING.' DEFAULT NULL',
            'work_time' =>Schema::TYPE_STRING.' DEFAULT NULL',
            'med_book' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE',
            'children' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE',
            'smoking' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE',
            'about_us_id' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'experience' => Schema::TYPE_TEXT.' DEFAULT NULL',
            'hobby' => Schema::TYPE_TEXT.' DEFAULT NULL',
            'date' => Schema::TYPE_DATE.' DEFAULT NULL',
            'state' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
        
        $this->createIndex(
            'index__personal_question__post_id', 
            '{{%personal_question}}', 
            'post_id'
        );
        
        $this->addForeignKey(
            'fk__personal_question__post_id', 
            '{{%personal_question}}', 
            'post_id',
            '{{%personal_settings_post}}', 
            'id'
        );  
        
        $this->createIndex(
            'index__personal_question__about_us_id', 
            '{{%personal_question}}', 
            'about_us_id'
        );
        
        $this->addForeignKey(
            'fk__personal_question__about_us_id', 
            '{{%personal_question}}', 
            'about_us_id',
            '{{%personal_about_us_value}}', 
            'id'
        );  
    }

    public function down()
    {
        $this->dropTable('{{%personal_question}}');
    }
    
}
