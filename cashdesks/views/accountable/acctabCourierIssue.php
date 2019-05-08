<?php

/* @var $this yii\web\View */

$this->title = 'Выдача денег курьеру';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Под отчёт',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-accountable-acctabCourierIssue">
    
    <?= $this->render('_form', [
        'model' => $model,
        'isPickup' => false,
        'couriersList' => $couriersList
    ]);?>
    
</div>