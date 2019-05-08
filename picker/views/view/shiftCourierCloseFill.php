<?php

/* @var $this yii\web\View */

$this->title = 'Смену курьера('.$model->courier_name.')';
$this->params['breadcrumbs'][] = [
    'url' => ['picker-index'], 
    'label' => 'Смена'
];
$this->params['breadcrumbs'][]= $this->title;

?>

<div class="picker-view-shiftCourierCloseFill">
    <?= $this->render('_shiftCourierFillForm', ['model' => $model]) ?>
</div>

