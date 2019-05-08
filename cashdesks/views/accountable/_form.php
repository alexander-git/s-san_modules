<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

?>
<div class="cashdesks-accountable-acctabCourierIssue">
    
    <div class="accountableTransactForm">
        <?php $form = ActiveForm::begin(); ?>

        <?php if (!$isPickup) : ?> 
        
        <?= $form->field($model, 'user_id')->widget(Select2::className(), [
            'data' => $couriersList,
            'pluginOptions' => [
                'allowClear' => false,
            ],
        ]) ?>
        
        <?php endif; ?>

        <?= $form->field($model, 'sum')->textInput(['maxlength' => true]) ?>
   
        <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>
             
        <div class="form-group">
            <?= Html::submitButton('Выполнить', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>
    
    
</div>