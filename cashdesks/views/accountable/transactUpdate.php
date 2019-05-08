<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */

$this->title = 'Обновить операцию';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Под отчёт',
];
$this->params['breadcrumbs'][] = [
    'url' => ['history'],
    'label' => 'История операций',
];
$this->params['breadcrumbs'][] = $this->title;

$needUsersList = false;
if ($model->isTypeAcctabIssue || $model->isTypeAcctabReturn) {
    if ($model->isAcctabCourier) { 
        $needUsersList = true;
    }
}

?>
<div class="cashdesks-accountable-transactUpdate">
    
    <div class="alert alert-info">
        Тип операции: <?=$model->typeName ?> 
        <?php if ($model->isAcctabCourier) : ?>
            - Комплектовщик
        <?php endif; ?>
        <?php if ($model->isAcctabPickup) : ?>
            - Самовывоз
        <?php endif; ?>
    </div>
    <div class="accountableTransactForm">
        
        <?php $form = ActiveForm::begin(); ?>

        <?php if ($needUsersList) : ?> 
        
        <?= $form->field($model, 'user_id')->widget(Select2::className(), [
            'data' => $usersList,
            'pluginOptions' => [
                'allowClear' => false,
            ],
        ]) ?>
        
        <?php endif; ?>

        <?= $form->field($model, 'sum')->textInput(['maxlength' => true]) ?>
   
        <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>
             
        <div class="form-group">
            <?= Html::submitButton('Обновить', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>
    
</div>