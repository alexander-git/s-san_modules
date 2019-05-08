<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Дополнительный курьер';
$this->params['breadcrumbs'][] = [
    'url' => ['picker-index'], 
    'label' => 'Смена'
];
$this->params['breadcrumbs'][] = [
    'url' => ['shift-courier-open-default'], 
    'label' => 'Открыть смену курьера'
];
$this->params['breadcrumbs'][]= $this->title;
?>
<div class="picker-view-shiftCourierOpenAdditional">

    <div class="shiftCourierForm">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'courier_name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'courier_phone')->textInput(['maxlength' => true]) ?>
           
        <div class="form-group">
            <?= Html::submitButton('Открыть', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
