<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Смена закрыта';
$this->params['breadcrumbs'][] = [
    'url' => ['picker-index'],
    'label' => 'Смена'
];
$this->params['breadcrumbs'][] = [
    'url' => ['shift-courier-close-fill', 'id' => $shiftsCourierModel->id], 
    'label' => 'Смена курьера('.$shiftsCourierModel->courier_name .')',
];
$this->params['breadcrumbs'][] = [
    'url' => ['shift-courier-close-banknotes', 'id' => $shiftsCourierModel->id], 
    'label' => 'Учёт наличных',
];

$this->params['breadcrumbs'][]= $this->title;
?>

<div class="picker-view-shiftCourierCloseSummary">
    <h1>Смена курьера закрыта</h1>
    
    <?= $this->render('_shiftCourierSummary', ['shiftsCourierModel' => $shiftsCourierModel]) ?>
    
    <p>
        <?=Html::a('Назад', ['shift-courier-close-banknotes', 'id' => $shiftsCourierModel->id], ['class' => 'btn btn-primary']) ?>
        <?=Html::a('Завершить', ['picker-index'], ['class' => 'btn btn-success']) ?>
    </p>
</div>
