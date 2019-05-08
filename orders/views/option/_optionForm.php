<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$isUpdating = !$model->isNewRecord;
if (!$isUpdating) {
    $buttonText = 'Создать';
    $buttonCssClass  = 'btn btn-success';
} else {
    $buttonText = 'Изменить';
    $buttonCssClass  = 'btn btn-primary';
}

$canChangeId = true;
if ($isUpdating) {
   if ($model->getOptionVals()->count() > 0) {
       $canChangeId = false;
   }
}

?>

<div class="optionForm">    
    <?php $form = ActiveForm::begin(); ?>

    <?php if ($canChangeId) : ?> 
        <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>
    <?php endif; ?>
    
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
 
    <div class="form-group">
        <?= Html::submitButton($buttonText, ['class' => $buttonCssClass]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
