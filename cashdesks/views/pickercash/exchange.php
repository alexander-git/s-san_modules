<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Размен купюр';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Касса комплектовщика',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-pickercash-exchange">
    
    <div class="pickercashTransactForm">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($pickercashTransactModel, 'desc')->textInput(['maxlength' => true]) ?>
        
        <?= $this->render('../common/_banknotesExchangeFormItems', [
            'form' => $form,
            'banknotesModel' => $banknotesModel
        ]) ?>
        
        <div class="form-group">
            <?= Html::submitButton('Выполнить', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>
    
</div>