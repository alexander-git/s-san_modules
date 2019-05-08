<?php

/* @var $this yii\web\View */
/* @var $shiftsCourierModel \app\modules\picker\models\ShiftsCourier */
/* @var $banknotesModel \app\models\picker\models\Banknotes */

$this->title = 'Учёт наличных';

$this->params['breadcrumbs'][] = [
    'url' => ['shift-index'], 
    'label' => 'Суточная смена'
];
$this->params['breadcrumbs'][] = [
    'url' => ['shift-courier-check-fill', 'id' => $shiftsCourierModel->id], 
    'label' => 'Cмена курьера('.$shiftsCourierModel->courier_name .')',
];
$this->params['breadcrumbs'][]= $this->title;
?>

<div class="picker-view-shiftCourierCheckBanknotes">
    
    <?= $this->render('_banknotesForm', [
        'shiftsCourierModel' => $shiftsCourierModel,
        'banknotesModel' => $banknotesModel,
        'previousPageUrl' => ['shift-courier-check-fill', 'id' => $shiftsCourierModel->id],
    ]); ?>
    
</div>