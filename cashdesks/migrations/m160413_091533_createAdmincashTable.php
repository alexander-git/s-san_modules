<?php

use yii\db\Schema;
use yii\db\Migration;

class m160413_091533_createAdmincashTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%cashdesks_admincash}}', [
            'id' => Schema::TYPE_PK,
            'depart_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'banknotes_id' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
        
            
        $this->createIndex(
            'index__cashdesks_admincash__banknotes_id', 
            '{{%cashdesks_admincash}}', 
            'banknotes_id'
        );
        
        $this->addForeignKey(
            'fk__cashdesks_admincash__banknotes_id', 
            '{{%cashdesks_admincash}}',  
            'banknotes_id', 
            '{{%cashdesks_banknotes}}', 
            'id'
        );  
    }
    
    public function down()
    {
        $this->dropTable('{{%cashdesks_admincash}}');
    }
    
}
