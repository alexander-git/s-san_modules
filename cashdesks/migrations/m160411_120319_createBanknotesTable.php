<?php

use yii\db\Schema;
use yii\db\Migration;

class m160411_120319_createBanknotesTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%cashdesks_banknotes}}', [
            'id' => Schema::TYPE_PK,
            'count_5000' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'count_1000' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'count_500' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'count_100' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'count_50' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'rest' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%cashdesks_banknotes}}');
    }
    
}
