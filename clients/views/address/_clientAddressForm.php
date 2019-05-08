<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\touchspin\TouchSpin;

$isUpdate = !$clientAddressModel->isNewRecord;

if (!$isUpdate) {
    $cssClass = 'btn btn-success';
} else {
    $cssClass = 'btn btn-primary';
}
$touchspinConfig = [
    'pluginOptions' => [
        'step' => 1,
        'min' => 0,
        'max' => 100000000,
        'verticalbuttons' => true,
        'verticalupclass' => 'glyphicon glyphicon-plus',
        'verticaldownclass' => 'glyphicon glyphicon-minus',
    ],
];

?>

<div class="clientAddressForm">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($isUpdate) : ?>
        <div class="alert alert-info">
            <?= $clientModel->fullname ?>
        </div>
    <?php endif; ?>
    
    <?php if(!$isUpdate) : ?>
    
        <?= $form->field($clientAddressModel, 'clientId')->widget(Select2::className(), [
            'data' => $clientsList,
            'pluginOptions' => [
                'allowClear' => false,
            ],
        ])?>
    
    <?php endif; ?>

    <?= $form->field($clientAddressModel, 'ordercount')->widget(TouchSpin::className(), $touchspinConfig) ?>

    <div class="form-group">
        <?= Html::submitButton('Выполнить', ['class' => $cssClass]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

