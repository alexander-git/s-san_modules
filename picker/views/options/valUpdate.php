<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Изменить ('.$model->option->label.')';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => 'Настройки ('.$departmentName.')', 
    'url' => ['val-index', 'departmentId' => $departmentId],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="picker-options-valUpdate">
       <h4>
            <?= $model->option->label ?>
       </h4>
    
       <div class="optionsValForm">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'val')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary', 'name'=> 'submitButton']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
