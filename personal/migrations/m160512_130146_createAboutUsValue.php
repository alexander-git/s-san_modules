<?php

use yii\db\Schema;
use yii\db\Migration;

class m160512_130146_createAboutUsValue extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%personal_about_us_value}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING.' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%personal_about_us_value}}');
    }
    
}
