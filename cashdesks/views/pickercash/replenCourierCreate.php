<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */

$this->title = 'Приём денег от курьера';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Касса комплектовщика',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-pickercash-replenCourierCreate">
    
    <div class="pickercashTransactForm">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($pickercashTransactModel, 'user_id')->widget(Select2::className(), [
            'data' => $couriersList,
            'pluginOptions' => [
                'allowClear' => false,
            ],
        ]) ?>

        <?= $form->field($pickercashTransactModel, 'desc')->textInput(['maxlength' => true]) ?>
        
        <?= $this->render('../common/_banknotesFormItems', [
            'form' => $form,
            'banknotesModel' => $banknotesModel
        ]) ?>
        
        <div class="form-group">
            <?= Html::submitButton('Выполнить', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>
    </div>
    
</div>