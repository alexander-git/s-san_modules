<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\touchspin\TouchSpin;

if ($addressModel->isNewRecord) {
    $buttonLabel = 'Создать';
    $cssClass = 'btn btn-success';
} else {
    $buttonLabel = 'Обновить';
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

<div class="addressForm">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($addressModel, 'cityId')->dropDownList($citiesList) ?>

    <?= $form->field($addressModel, 'street')->textInput(['maxlength' => true]) ?>

    <?= $form->field($addressModel, 'home')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($addressModel, 'appart')->textInput() ?>
    
    <?= $form->field($addressModel, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($addressModel, 'entrance')->textInput() ?>

    <?= $form->field($addressModel, 'floor')->textInput() ?>
    
    <?= $form->field($addressModel, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($addressModel, 'desc')->textarea(['rows' => 6]) ?>
    
    <?= $form->field($clientAddressModel, 'ordercount')->widget(TouchSpin::className(), $touchspinConfig) ?>

    <div class="form-group">
        <?= Html::submitButton($buttonLabel, ['class' => $cssClass]) ?>
    </div>
                    
    <?php ActiveForm::end(); ?>

</div>
