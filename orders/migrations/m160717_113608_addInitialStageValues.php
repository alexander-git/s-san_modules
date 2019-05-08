<?php

use yii\db\Migration;

class m160717_113608_addInitialStageValues extends Migration
{
    
    public function up()
    {
        $this->batchInsert('{{%orders_stage}}', ['name', 'sort'], [
            ['Новый', 1],
            ['Принят', 2],
            ['В работе', 3],
            ['Доставляется', 4],
            ['Доставлен', 5],
            ['Отменён', 6],
        ]);
    }

    public function down()
    {   
        /*
        $this->delete('{{%orders_stage}}', ['in', 'name', [
            'Новый',
            'Принят',
            'В работе',
            'Доставляется',
            'Доставлен',
            'Отменён',
        ]]); 
        */
    }
    
}
