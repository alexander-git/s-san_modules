<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Изменить смену курьера';
$this->params['breadcrumbs'][] = [
    'url' => ['picker-index'], 
    'label' => 'Смена'
];
$this->params['breadcrumbs'][]= $this->title;
?>
<div class="picker-view-shiftCourierUpdateAdditional">
    
    <div class="shiftCourierForm">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'courier_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'courier_phone')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Изменить', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>


