<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Итог';
$this->params['breadcrumbs'][] = [
    'url' => ['shif-index'], 
    'label' => 'Суточная смена'
];
$this->params['breadcrumbs'][] = [
    'url' => ['shift-courier-check-fill', 'id' => $shiftsCourierModel->id], 
    'label' => 'Смена курьера('.$shiftsCourierModel->courier_name .')',
];
$this->params['breadcrumbs'][] = [
    'url' => ['shift-courier-check-banknotes', 'id' => $shiftsCourierModel->id], 
    'label' => 'Учёт наличных',
];

$this->params['breadcrumbs'][]= $this->title;
?>

<div class="picker-view-shiftCourierCheckSummary">
    
    <?= $this->render('_shiftCourierSummary', ['shiftsCourierModel' => $shiftsCourierModel]) ?>
    
    <p>
        <?=Html::a('Назад', ['shift-courier-check-banknotes', 'id' => $shiftsCourierModel->id], ['class' => 'btn btn-primary']) ?>
        <?=Html::a('К суточной смене', ['shift-index'], ['class' => 'btn btn-success']) ?>
    </p>
</div>
