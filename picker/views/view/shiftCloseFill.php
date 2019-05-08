<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Ввод данных';

$this->params['breadcrumbs'][] = [
    'url' => ['shift-index'], 
    'label' => 'Суточная смена'
];

$this->params['breadcrumbs'][]= $this->title;

?>

<div class="picker-view-shiftCloseFill">
    <div class="shiftForm">
        <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'prog_turn')->textInput() ?>
        
            <?= $form->field($model, 'prog_turn_nocash')->textInput() ?>
        
            <?= $form->field($model, 'prog_check_count')->textInput() ?>
        
            <?= $form->field($model, 'turn_cashdesk')->textInput() ?>
        
        <div class="form-group">
            <?= Html::submitButton('Далее', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>

