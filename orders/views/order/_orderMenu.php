<?php

use yii\helpers\Html;

?>
<div class="orderMenu">
    <p>
        <?= Html::a('Меню', ['menu', 'orderId' => $orderModel->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Корзина', ['cart', 'orderId' => $orderModel->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Имя клиента', ['client-name', 'orderId' => $orderModel->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Телефон', ['client-phone', 'orderId' => $orderModel->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Адрес', ['client-address', 'orderId' => $orderModel->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Бонусы', ['bonuses', 'orderId' => $orderModel->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Инфонрмация', ['info', 'orderId' => $orderModel->id], ['class' => 'btn btn-primary']) ?>        
        <?= Html::a('Итог', ['summary', 'orderId' => $orderModel->id], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <?php if (isset($needAdditionalButtons) && $needAdditionalButtons) : ?>
        <p>  
            <?= Html::a('Редактировать', ['update', 'orderId' => $orderModel->id], ['class' => 'btn btn-primary'] ) ?> 
         
            
            <?= Html::a('Удалить', ['delete', 'orderId' => $orderModel->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>  
            
            
            
        </p>  
    <?php endif; ?>
    
</div>