<?php

use yii\db\Migration;

class m160325_190900_addInitialOptions extends Migration
{
    public function up()
    {
        $this->batchInsert('{{%picker_options}}', ['id', 'label'], [
            ['pay_day_courier','Оплата дневному курьеру'],
            ['pay_even_courier', 'Оплата вечернему курьеру'],
            ['pay_dop_courier', 'Оплата доп. курьеру'],
            ['pay_trip', 'Оплата за доп. поездки'],
        ]);
    }

    public function down()
    {   
        $initialOptions = [
            'pay_day_courier',
            'pay_even_courier',
            'pay_dop_courier',
            'pay_trip',
        ];
        
        $this->delete('{{%picker_options_val}}', ['in', 'opt_id', $initialOptions]);
        $this->delete('{{%picker_options}}', ['in', 'id', $initialOptions]);
    }

}
