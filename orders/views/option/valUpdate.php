<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Обновить ('.$model->option_id.')';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['settings/index']];
$this->params['breadcrumbs'][] = ['label' => 'Основные настройки', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $cityName, 'url' => ['val-index', 'cityId' => $cityId]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-option-valUpdate">
    <h4>
         <?= $model->option->name ?>
    </h4>
    
    <div class="optionValForm">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
    
</div>
