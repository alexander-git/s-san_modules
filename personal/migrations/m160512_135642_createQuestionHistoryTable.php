<?php

use yii\db\Schema;
use yii\db\Migration;

class m160512_135642_createQuestionHistoryTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%personal_question_history}}', [
            'id' => Schema::TYPE_PK,
            'question_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'date_change' => Schema::TYPE_INTEGER.' NOT NULL',
            'text' => Schema::TYPE_STRING.' DEFAULT NULL',
            'state' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
        
        $this->createIndex(
            'index__personal_question__question_id', 
            '{{%personal_question_history}}', 
            'question_id'
        );
        
        $this->addForeignKey(
            'fk__personal_question_history__question_id', 
            '{{%personal_question_history}}', 
            'question_id',
            '{{%personal_question}}', 
            'id'
        );  
    }

    public function down()
    {
        $this->dropTable('{{%personal_question_history}}');
    }
    
}
