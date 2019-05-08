<?php

use yii\db\Schema;
use yii\db\Migration;

class m160512_125610_createVacancyTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%personal_vacancy}}', [
            'id' => Schema::TYPE_PK,
            'text' => Schema::TYPE_TEXT.' DEFAULT NULL',
            'post_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'user_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'depart_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'state' => Schema::TYPE_INTEGER.' NOT NULL',
            'date_create' => Schema::TYPE_INTEGER.' NOT NULL',
            'date_public' => Schema::TYPE_INTEGER.' DEFAULT NULL',
        ], $tableOptions);
        
        $this->createIndex(
            'index__personal_vacancy__post_id', 
            '{{%personal_vacancy}}', 
            'post_id'
        );
        
        $this->addForeignKey(
            'fk__personal_vacancy__post_id', 
            '{{%personal_vacancy}}', 
            'post_id',
            '{{%personal_settings_post}}', 
            'id'
        );  
    }

    public function down()
    {
        $this->dropTable('{{%personal_vacancy}}');
    }
    
}
