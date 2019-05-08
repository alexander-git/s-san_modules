<?php

use yii\db\Migration;

class m160717_185809_addInitialOptionValues extends Migration
{
    private $initialOptions = [
        'expiry_time' => 'время в минутах, после которого новый заказ считается просроченным',
        'delivery_time' => 'время в минутах за которое заказ должен быть доставлен клиенту',
        'min_total_price_for_free_delivery' => 'минимальная стоимость заказа для бесплатной доставки',
        'delivery_cost' => 'стоимость доставки',
        'min_possible_dilivery_time' => 'время первой возможной доставки',
        'max_order_acceptance_time' => 'время последнего возможного принятия заказа',
        'secret_key' => 'секретный ключ',
    ];
    
    public function up()
    {
        $optionsToInsert = [];
        foreach ($this->initialOptions as $id => $name) {
            $optionsToInsert []= [$id, $name];
        }
        
        $this->batchInsert('{{%orders_options}}', ['id', 'name'], $optionsToInsert);
    }

    public function down()
    {   
        $ids = array_keys($this->initialOptions);
        
        $this->delete('{{%orders_option_vals}}', ['in', 'option_id', $ids]);
        $this->delete('{{%orders_options}}', ['in', 'id', $ids]);
    }
}
