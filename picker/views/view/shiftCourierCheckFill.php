<?php

/* @var $this yii\web\View */

$this->title = 'Смена курьера('.$model->courier_name.')';
$this->params['breadcrumbs'][] = [
    'url' => ['shift-index'], 
    'label' => 'Суточная смена'
];
$this->params['breadcrumbs'][]= $this->title;

?>

<div class="picker-view-shiftCourierCheckFill">
       <?= $this->render('_shiftCourierFillForm', ['model' => $model]) ?>
</div>

