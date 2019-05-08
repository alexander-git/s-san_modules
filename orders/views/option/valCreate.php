<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['settings/index']];
$this->params['breadcrumbs'][] = ['label' => 'Основные настройки', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $cityName, 'url' => ['val-index', 'cityId' => $cityId]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="picker-options-valCreate">

    <div class="optionValForm">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'option_id')->dropdownList($optionsList) ?>

        <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>


        <div class="form-group">
            <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
