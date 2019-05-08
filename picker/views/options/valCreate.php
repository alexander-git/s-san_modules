<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Создать';
$this->params['breadcrumbs'][] = [
    'label' => 'Настройки', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = [
    'label' => 'Настройки ('.$departmentName.')', 
    'url' => ['val-index', 'departmentId' => $departmentId],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="picker-options-valCreate">

    <div class="optionsValForm">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'opt_id')->dropdownList($optionsList) ?>

        <?= $form->field($model, 'val')->textInput(['maxlength' => true]) ?>


        <div class="form-group">
            <?= Html::submitButton('Создать', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
