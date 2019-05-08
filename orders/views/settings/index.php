<?php

use yii\helpers\Html;


/* @var $this yii\web\View */

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-settings-index">
    
    <p>
        <?= Html::a('Основыне настройки', ['option/index'], ['class' => 'btn btn-primary']) ?>
    </p>
   
    <p>
        <?= Html::a('Стадии заказов', ['stage/index'], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <p>
        <?= Html::a('Типы оплаты', ['payment-type/index'], ['class' => 'btn btn-primary']) ?>
    </p>
    
</div>
