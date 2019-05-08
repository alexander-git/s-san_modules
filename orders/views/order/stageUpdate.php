<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Обновить статус';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $orderModel->id, 'url' => ['view', 'orderId' => $orderModel->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="orders-order-stageUpdate">
    
    <div class="logRecordForm">

        <?php $form = ActiveForm::begin(); ?> 

            <?= $form->field($logRecordModel, 'stage_id')->dropdownList($stagesList) ?>
        
            <?= $form->field($logRecordModel, 'comment')->textInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>