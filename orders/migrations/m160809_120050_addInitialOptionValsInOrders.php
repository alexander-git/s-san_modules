<?php

use yii\db\Migration;

class m160809_120050_addInitialOptionValsInOrders extends Migration
{
    
    private $initialOptionVals = [
        'expiry_time' => 120,
        'delivery_time' => 60,
        'min_total_price_for_free_delivery' => 1000,
        'delivery_cost' => 100,
        'min_possible_dilivery_time' => '08:00',
        'max_order_acceptance_time' => '23:00',
        'secret_key' => 'password',
    ];
    
    const DEFAULT_CITY_ID = 0;
    
    public function up()
    {
        $valsToInsert = [];
        foreach ($this->initialOptionVals as $id => $value) {
            $valsToInsert []= [$id, $value, self::DEFAULT_CITY_ID];
        }
        
        
        $this->batchInsert(
            '{{%orders_option_vals}}', 
            ['option_id', 'value', 'city_id'], 
            $valsToInsert
        );
    }

    public function down()
    {
        $ids = array_keys($this->initialOptionVals);
        
        $this->delete('{{%orders_option_vals}}', [
            'and',
            ['in', 'option_id', $ids],
            ['=', 'city_id', self::DEFAULT_CITY_ID],
        ]);
    }
    
}
