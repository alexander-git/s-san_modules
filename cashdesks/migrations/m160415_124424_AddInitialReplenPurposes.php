<?php

use yii\db\Schema;
use yii\db\Migration;

class m160415_124424_AddInitialReplenPurposes extends Migration
{
    public function up()
    {
        $this->batchInsert('{{%cashdesks_replen_purpose}}', ['name'], [
            ['Зарплата персоналу'],
            ['Постоянные расходы'],
            ['Переменные расходы'],
        ]);
    }

    public function down()
    {   
        $this->delete('{{%cashdesks_replen_purpose}}', [
            'name' => [
                'Зарплата персоналу', 
                'Постоянные расходы', 
                'Переменные расходы',
            ],
        ]);
    }
    
}
