<?php

use yii\widgets\DetailView;

if (!isset($showBeforeAcceptReject) ) {
    $showBeforeAcceptReject = false;
}

$attributes = [];

$attributes []= [
    'attribute' => 'depart_id',
    'value' => $model->departmentName,
];
$attributes []= [
    'attribute' => 'type',
    'value' => $model->typeNameAdvanced,
];

if (!$showBeforeAcceptReject) {
    $attributes []=  [
        'attribute' => 'state',
        'value' => $model->stateName,
    ];
}

$attributes []= [
    'attribute' => 'picker_id',
    'value' => $model->pickerName
];

if (!$showBeforeAcceptReject) {
    $attributes []= [
        'attribute' => 'user_id',
        'value' => $model->userName,
    ];
}

$attributes []= [
    'attribute' => 'date_create',
    'format' => ['datetime', 'php:d-m-Y H:i:s'],
];

if (!$showBeforeAcceptReject) {
    $attributes []= [
        'attribute' => 'date_end',
        'format' => ['datetime', 'php:d-m-Y H:i:s'],
    ];
}

$attributes []= 'desc';  
$attributes []= 'banknotes.count_5000';
$attributes []= 'banknotes.count_1000';
$attributes []= 'banknotes.count_500';
$attributes []= 'banknotes.count_100';
$attributes []= 'banknotes.count_50';
$attributes []= 'banknotes.rest';
$attributes []= 'banknotes.sum';

?>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => $attributes,
]) ?>
       