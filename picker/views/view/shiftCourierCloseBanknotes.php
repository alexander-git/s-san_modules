<?php

/* @var $this yii\web\View */
/* @var $shiftsCourierModel \app\modules\picker\models\ShiftsCourier */
/* @var $banknotesModel \app\models\picker\models\Banknotes */


$this->title = 'Учёт наличных';
$this->params['breadcrumbs'][] = [
    'url' => ['picker-index'], 
    'label' => 'Смена'
];
$this->params['breadcrumbs'][] = [
    'url' => ['shift-courier-close-fill', 'id' => $shiftsCourierModel->id], 
    'label' => 'Смена курьера('.$shiftsCourierModel->courier_name .')',
];
$this->params['breadcrumbs'][]= $this->title;
?>

<div class="picker-view-shiftCourierCloseBanknotes">
    
    <?= $this->render('_banknotesForm', [
        'shiftsCourierModel' => $shiftsCourierModel,
        'banknotesModel' => $banknotesModel,
        'previousPageUrl' => ['shift-courier-close-fill', 'id' => $shiftsCourierModel->id],
    ]); ?>
    
</div>