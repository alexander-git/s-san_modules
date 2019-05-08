<?php

use yii\db\Schema;
use yii\db\Migration;

class m160413_092416_createPickercashTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%cashdesks_pickercash}}', [
            'id' => Schema::TYPE_PK,
            'depart_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'banknotes_id' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
        
            
        $this->createIndex(
            'index__cashdesks_pickercash__banknotes_id', 
            '{{%cashdesks_pickercash}}', 
            'banknotes_id'
        );
        
        $this->addForeignKey(
            'fk__cashdesks_pickercash__banknotes_id', 
            '{{%cashdesks_pickercash}}',  
            'banknotes_id', 
            '{{%cashdesks_banknotes}}', 
            'id'
        );  
    }
    
    public function down()
    {
        $this->dropTable('{{%cashdesks_pickercash}}');
    }
    
}
