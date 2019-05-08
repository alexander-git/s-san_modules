<?php

use yii\db\Schema;
use yii\db\Migration;

class m160512_170218_createMedbookTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%personal_medbook}}', [
            'card_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'date_sanmin' => Schema::TYPE_DATE.' DEFAULT NULL',
            'date_sanmin_end' => Schema::TYPE_DATE.' DEFAULT NULL',
            'date_exam' => Schema::TYPE_DATE.' DEFAULT NULL',
            'date_exam_end' => Schema::TYPE_DATE.' DEFAULT NULL',
        ], $tableOptions);
        
        $this->addPrimaryKey('card_id', '{{%personal_medbook}}', 'card_id');
        
        $this->createIndex(
            'index__personal_medbook__card_id', 
            '{{%personal_medbook}}', 
            'card_id'
        );
        
        $this->addForeignKey(
            'fk__personal_medbook__card_id', 
            '{{%personal_medbook}}', 
            'card_id',
            '{{%personal_card}}', 
            'id'
        );
    }

    public function down()
    {
        $this->dropTable('{{%personal_medbook}}');
    }
    
}
