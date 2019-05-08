<?php

/* @var $this yii\web\View */

$this->title = 'Возврат денег от курьера';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Под отчёт',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-accountable-acctabCourierReturn">
    
    <?= $this->render('_form', [
        'model' => $model,
        'isPickup' => false,
        'couriersList' => $couriersList
    ]);?>
    
</div>