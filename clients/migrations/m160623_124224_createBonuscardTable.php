use yii\db\Schema;
<?php

use yii\db\Schema;
use yii\db\Migration;

class m160623_124224_createBonuscardTable extends Migration
{
    public function up()
    {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%clients_bonuscard}}', [
            'id' => Schema::TYPE_PK,
            'type' => Schema::TYPE_INTEGER.' NOT NULL',
            'moneyquan' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'bonuses' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
        ], $tableOptions);
        
        
        $this->createIndex(
            'index-clients_bonuscard-type', 
            '{{%clients_bonuscard}}',  
            'type'
        );
        $this->addForeignKey(
            'fk-clients_bonuscard-type', 
            '{{%clients_bonuscard}}', 
            'type', 
            '{{%clients_bonuscard_type}}', 
            'id'
        );   
    }

    public function down()
    {
        $this->dropTable('{{%clients_bonuscard}}');
    }
    
}
