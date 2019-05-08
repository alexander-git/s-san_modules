<?php

use yii\db\Migration;

class m160830_170837_addStationOrdersCountOptionVal extends Migration
{
     private $optionVals = [
        'station_orders_count' => 3,
    ];
    
    const DEFAULT_CITY_ID = 0;
    
    public function up()
    {
        $valsToInsert = [];
        foreach ($this->optionVals as $id => $value) {
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
        $ids = array_keys($this->optionVals);
        
        $this->delete('{{%orders_option_vals}}', [
            'and',
            ['in', 'option_id', $ids],
            ['=', 'city_id', self::DEFAULT_CITY_ID],
        ]);
    }
    
    
}
