<?php

/* @var $this yii\web\View */

$this->title = 'Взять для самовывоза';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Под отчёт',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-accountable-acctabPickupIssue">
    
    <?= $this->render('_form', [
        'model' => $model,
        'isPickup' => true,
    ]);?>
    
</div>