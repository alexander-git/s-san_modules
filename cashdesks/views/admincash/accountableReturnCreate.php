<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Изъять';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Сейф администратора',
];
$this->params['breadcrumbs'][] = [
    'url' => ['accountable-index'],
    'label' => 'Касса "Под отчёт"',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-admincash-accountableReturnCreate">
    
    <div class="admincahsTransactForm">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($admincashTransactModel, 'desc')->textInput(['maxlength' => true]) ?>
        
        <?= $this->render('../common/_banknotesFormItems', [
            'form' => $form,
            'banknotesModel' => $banknotesModel
        ]) ?>
        
        <div class="form-group">
            <?= Html::submitButton('Выполнить', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>
    
</div>