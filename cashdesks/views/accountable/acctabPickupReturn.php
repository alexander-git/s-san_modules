<?php

/* @var $this yii\web\View */

$this->title = 'Возврат самовывоза';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Под отчёт',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-accountable-acctabPickupReturn">
    
       
    <?= $this->render('_form', [
        'model' => $model,
        'isPickup' => true,
    ]);?>
    
    
</div>