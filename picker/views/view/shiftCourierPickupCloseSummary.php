<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Смена самовывоза закрыта';
$this->params['breadcrumbs'][] = [
    'url' => ['shift-index'], 
    'label' => 'Суточная смена'
];
$this->params['breadcrumbs'][] = [
    'url' => ['shift-courier-pickup-close-fill'], 
    'label' => 'Самовывоз',
];
$this->params['breadcrumbs'][]= [
    'url' => ['shift-courier-pickup-close-banknotes'],
    'label' => 'Учёт наличных',
];
$this->params['breadcrumbs'][]= $this->title;
?>

<div class="picker-view-shiftCourierPickupCloseSummary">
    <h1>Смена самовывоза закрыта</h1>
    
    <?= $this->render('_shiftCourierSummary', ['shiftsCourierModel' => $shiftsCourierModel]) ?>
    
    <p>
        <?=Html::a('Назад', ['shift-courier-pickup-close-banknotes'], ['class' => 'btn btn-primary']) ?>
        <?=Html::a('К суточной смене', ['shift-index'], ['class' => 'btn btn-success']) ?>
    </p>
</div>
