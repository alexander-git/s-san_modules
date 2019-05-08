<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\cashdesks\models\ExpenseType;

$listOptions = [];

if ($model->isNewRecord) {
    $buttonLabel = 'Создать';
    $cssClass = 'btn btn-success';        
} else {
    $buttonLabel = 'Изменить';
    $cssClass = 'btn btn-primary';

    $listOptions['disabled'] = 'disabled';
}

?>
<div class="expenseTypeForm">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropdownList(ExpenseType::getTypesArray(), $listOptions) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($buttonLabel, ['class' => $cssClass, 'name'=> 'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>