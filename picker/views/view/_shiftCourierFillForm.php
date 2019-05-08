<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\picker\models\ShiftsCourier;

/* @var $model \app\modules\picker\models\ShiftsCourier */

?>

<div class="shiftCourierForm">
    <?php $form = ActiveForm::begin(); ?>
    
        <?php if ($model->isTypeCourierDefault) : ?>
            <?= $form->field($model, 'type_courier')->dropDownList(ShiftsCourier::getTypeCouriersArrayDefault()) ?>
        <?php endif; ?>

        <?= $form->field($model, 'check_sum')->textInput() ?>

        <?= $form->field($model, 'check_nocash')->textInput() ?>

        <?= $form->field($model, 'count_order')->textInput() ?>
    
        <?php if ($model->isTypeCourierDefault || $model->isTypeCourierAdditional) : ?>
            <?= $form->field($model, 'count_trip')->textInput() ?>
        <?php endif; ?>
    
        <?php if ($model->isTypeCourierDefault) : ?>
            <?= $form->field($model, 'spend')->textInput() ?>
        <?php endif; ?>

        <?= $form->field($model, 'gifts')->textInput() ?>

        <?= $form->field($model, 'message')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton('Далее', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>