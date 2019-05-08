<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="optionsForm">    
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>
    
 
    <div class="form-group">
        <?php
            if ($model->isNewRecord) {
                $buttonText = 'Создать';
                $buttonCssClass  = 'btn btn-success';
            } else {
                $buttonText = 'Изменить';
                $buttonCssClass  = 'btn btn-primary';
            }
        ?>
        <?= Html::submitButton($buttonText, ['class' => $buttonCssClass, 'name'=> 'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
