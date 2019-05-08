<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Закрыть смену';
$this->params['breadcrumbs'][] = [
    'url' => ['picker-index'], 
    'label' => 'Смена'
];
$this->params['breadcrumbs'][]= $this->title;

?>

<div class="picker-view-shiftPickerClose">
    <div class="shiftPickerCloseForm">
        
        <?php $form = ActiveForm::begin(); ?>
            
            <?= $form->field($model, 'closeFlag')->hiddenInput()->label(false) ?>
            
            <?php if ($model->getShiftsCourierOpenedCount() > 0) : ?>
                <p class="alert alert-danger">
                    Незакрытых смен курьеров - <?= $model->getShiftsCourierOpenedCount() ?>.<br />
                    Передать их другому комплектовщику и закрыть смену?
                </p>
                <div class="form-group">
                    <?= $form->field($model, 'pickerId')->dropDownList($pickersList) ?>
                </div>
            <?php else : ?>
                <p class="alert alert-danger">
                   Вы уверены?
                </p>
            <?php endif; ?>
                
            <div class="form-group">
                <?= Html::a('Назад', ['picker-index'], ['class' => 'btn btn-primary']) ?>
                <?= Html::submitButton('Закрыть смену', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
            </div>
                
        <?= Html::endForm(); ?>
                
    </div>
</div>

