<?php

use yii\widgets\DetailView;

/* @var $shiftsCourierModel \app\modules\picker\models\ShiftsCourier */

$attributes = [];
$attributes []= 'courier_name';
$attributes []= [
    'attribute' => 'type_courier',
    'value' => $shiftsCourierModel->typeCourierName,
];
$attributes [] = [
    'attribute' => 'date_open',
    'format' => ['datetime', 'php:d-m-Y H:i:s'],  
];
$attributes []= [
    'attribute' => 'date_close',
    'format' => ['datetime', 'php:d-m-Y H:i:s'],        
];

$attributes []= 'check_sum';
$attributes []= 'check_nocash';
$attributes []= 'count_order';

if (!$shiftsCourierModel->isTypeCourierPickup) {
    $attributes []= 'count_trip';
}

if ($shiftsCourierModel->isTypeCourierDefault) {
    $attributes []= 'spend';
}

$attributes []= 'gifts';

if (!$shiftsCourierModel->isTypeCourierPickup) {
    $attributes []= 'payment';
}

$attributes []= 'cash';
$attributes []= 'cashRequired';
$attributes []= 'cashBalance';

?>
<div class="row">
    <div class="col-lg-12">
    <?= DetailView::widget([
        'model' => $shiftsCourierModel,
        'attributes' => $attributes,
    ]) ?>
    </div>
</div>