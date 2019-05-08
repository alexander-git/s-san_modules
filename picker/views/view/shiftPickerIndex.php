<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

if ($isShiftPickerClosed) {
    $this->title = 'Смена закрыта';
} else {
    $this->title = 'Открыть смену';
}

$this->params['breadcrumbs'][]= $this->title;
?>
<div class="picker-view-shiftPickerIndex">
    
    <?php if ($isShiftPickerClosed) : ?>
        <p class="alert alert-info">
            Смена закрыта. Пока не будет закрыта суточная смена открытие 
            новой смены невозможно.
        </p>
    <?php elseif ($isShiftCheckingByMainPicker) : ?>
        <p class="alert alert-info">
            В данный момент открытие смены невозможно.
        </p>
    <?php else : ?>
        <p>
            <?= Html::a('Открыть смену', ['shift-picker-open'], ['data-method' => 'post', 'class' => 'btn btn-primary']) ?>
        </p>
    <?php endif; ?>
    
</div>


