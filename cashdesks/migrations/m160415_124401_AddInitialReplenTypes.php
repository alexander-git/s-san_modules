<?php

use yii\db\Schema;
use yii\db\Migration;

class m160415_124401_AddInitialReplenTypes extends Migration
{
    public function up()
    {
        $this->batchInsert('{{%cashdesks_replen_type}}', ['name'], [
            ['Бухгалтерия'],
            ['Банк'],
        ]);
    }

    public function down()
    {   
        $this->delete('{{%cashdesks_replen_type}}', [
            'name' => [
                'Бухгалтерия', 
                'Банк', 
            ],
        ]);
    }
    
}
