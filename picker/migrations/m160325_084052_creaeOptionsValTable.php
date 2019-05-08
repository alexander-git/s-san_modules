<?php

use yii\db\Schema;
use yii\db\Migration;

class m160325_084052_creaeOptionsValTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%picker_options_val}}', [
            'opt_id' => Schema::TYPE_STRING.' NOT NULL',
            'val' => Schema::TYPE_STRING.' DEFAULT NULL',
            'depart_id' => Schema::TYPE_INTEGER.' NOT NULL',
        ], $tableOptions);
        
        $this->addPrimaryKey(
            'pk__picker_options_val', 
            '{{%picker_options_val}}', 
            ['opt_id', 'depart_id']
        );
        
        $this->createIndex(
            'index__picker_options_val__opt_id', 
            '{{%picker_options_val}}',  
            'opt_id'
        );
        $this->addForeignKey(
            'fk__picker_options_val__opt_id', 
            '{{%picker_options_val}}', 
            'opt_id', 
            '{{%picker_options}}', 
            'id'
        );       
    }

    public function down()
    {
        $this->dropTable('{{%picker_options_val}}');
    }
    
}
