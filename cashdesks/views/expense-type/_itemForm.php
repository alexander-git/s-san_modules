<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

if ($expenseTypeItemModel->isNewRecord) {
    $buttonLabel = 'Добавить';
    $cssClass = 'btn btn-success';        
} else {
    $buttonLabel = 'Изменить';
    $cssClass = 'btn btn-primary';    
}

?>
<div class="expenseTypeItemForm">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($expenseTypeItemModel, 'value')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($buttonLabel, ['class' => $cssClass, 'name'=> 'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>