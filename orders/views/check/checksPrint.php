<?php

use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = 'Печать чеков для заказа № '.$model->order_num;

?>
<div class="orders-cheks-checksPrint">
    
    <h3><?= $this->title ?></h3>
    <br />
    <p>
        <?=Html::a('Чек курьеру', ['print-check-courier', 'orderId' => $model->id], ['class' => 'btn btn-primary', 'target' => '_blank']) ?>
        <?=Html::a('Чек клиенту', ['print-check-client', 'orderId' => $model->id], ['class' => 'btn btn-primary', 'target' => '_blank']) ?>
     </p>
    
</div>
