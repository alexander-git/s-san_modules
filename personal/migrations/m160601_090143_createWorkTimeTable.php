<?php

use yii\db\Schema;
use yii\db\Migration;

class m160601_090143_createWorkTimeTable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%personal_work_time}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'date' => Schema::TYPE_DATE.' NOT NULL',
            'from' => 'CHAR(5) NOT NULL',
            'to' => 'CHAR(5) NOT NULL',
        ], $tableOptions);
        
        $this->createIndex(
            'index__work_time__user_id_date', 
            '{{%personal_work_time}}' ,
            ['user_id', 'date']
        );
    }
    
    public function down()
    {
        $this->dropTable('{{%personal_work_time}}');
    }
    
}
