<?php

/* @var $this yii\web\View */

$this->title = 'Самовывоз';
$this->params['breadcrumbs'][] = [
    'url' => ['shift-index'], 
    'label' => 'Суточная смена'
];
$this->params['breadcrumbs'][]= $this->title;

?>

<div class="picker-view-shiftCourierPickupCloseFill">
       <?= $this->render('_shiftCourierFillForm', ['model' => $model]) ?>
</div>