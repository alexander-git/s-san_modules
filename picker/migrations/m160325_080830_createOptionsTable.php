<?php

use yii\db\Schema;
use yii\db\Migration;

class m160325_080830_createOptionsTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%picker_options}}', [
            'id' => Schema::TYPE_STRING.' PRIMARY KEY',
            'label' => Schema::TYPE_STRING.' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%picker_options}}');
    }
    
}
