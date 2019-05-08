<?php

use yii\db\Migration;

class m160717_114424_addInitialPaymentValues extends Migration
{
    public function up()
    {
        $this->batchInsert('{{%orders_payment}}', ['name', 'sort'], [
            ['Наличными курьеру', 1],
            ['Картой курьеру', 2],
            ['Безнал. сбербанк', 3],
            ['Безнал. яндекс', 4],
        ]);
    }

    public function down()
    {   
        /*
        $this->delete('{{%orders_payment}}', ['in', 'name', [
            'Наличными курьеру',
            'Картой курьеру',
            'Безнал. сбербанк',
            'Безнал. яндекс',
        ]]); 
        */
    }
    
}
