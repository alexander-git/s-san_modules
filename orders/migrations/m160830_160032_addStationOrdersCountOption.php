<?php


use yii\db\Migration;

class m160830_160032_addStationOrdersCountOption extends Migration
{
   
    private $options = [
        'station_orders_count' => 'cколько отображать заказов на станциях приготовления пищи',
    ];
    
    public function up()
    {
        $optionsToInsert = [];
        foreach ($this->options as $id => $name) {
            $optionsToInsert []= [$id, $name];
        }
        
        $this->batchInsert('{{%orders_options}}', ['id', 'name'], $optionsToInsert);
    }

    public function down()
    {   
        $ids = array_keys($this->options);
        
        $this->delete('{{%orders_option_vals}}', ['in', 'option_id', $ids]);
        $this->delete('{{%orders_options}}', ['in', 'id', $ids]);
    }
}
