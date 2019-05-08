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
    'url' => ['shift-courier-pickup-close-fill'], 
    'label' => 'Самовывоз',
];
$this->params['breadcrumbs'][]= $this->title;
?>

<div class="picker-view-shiftCourierPickupCloseBanknotes">
    
    <?= $this->render('_banknotesForm', [
        'shiftsCourierModel' => $shiftsCourierModel,
        'banknotesModel' => $banknotesModel,
        'previousPageUrl' => ['shift-courier-pickup-close-fill'],
    ]); ?>
    
</div>