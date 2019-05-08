<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Редактировать';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $orderModel->id, 'url' => ['view', 'orderId' => $orderModel->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="orders-order-update">
    
    <div class="orderForm">

        <?php $form = ActiveForm::begin(); ?> 
        
            <?= $form->field($orderModel, 'recipient')->textInput(['maxlength' => true]) ?> 
        
            <?= $form->field($orderModel, 'phone')->textInput(['maxlength' => true]) ?>
        
            <?= $form->field($orderModel, 'alter_phone')->textInput(['maxlength' => true]) ?>
        
            <?= $form->field($orderModel, 'person_num')->textInput(['maxlength' => true]) ?>
        
            <?= $form->field($orderModel, 'payment_type')->dropDownList($paymentTypesList) ?>
        
            <?php 
            /*
            <?= $form->field($orderModel, 'city_id')->dropDownList($citiesList) ?>
            */ 
            ?>
            <?= $form->field($orderModel, 'address')->textInput(['maxlength' => true]) ?>
        
            <?= $form->field($orderModel, 'comment')->textInput(['maxlength' => true]) ?>
        
            <div class="form-group">
                <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>