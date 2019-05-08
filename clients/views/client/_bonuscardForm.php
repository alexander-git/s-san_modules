<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

if ($model->isNewRecord) {
    $buttonLabel = 'Создать';
    $cssClass = 'btn btn-success';
} else {
    $buttonLabel = 'Обновить';
    $cssClass = 'btn btn-primary';
}

?>

<div class="bonuscardTypeForm">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList($bonuscardTypesList) ?>

    <?= $form->field($model, 'moneyquan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bonuses')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($buttonLabel, ['class' => $cssClass]) ?>
    </div>
                    
    <?php ActiveForm::end(); ?>

</div>
