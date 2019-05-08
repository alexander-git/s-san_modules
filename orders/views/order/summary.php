<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\orders\assets\OrderCssAsset;

/* @var $this yii\web\View */

$this->title = 'Итог';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $orderModel->id, 'url' => ['view', 'orderId' => $orderModel->id]];
$this->params['breadcrumbs'][] = $this->title;

OrderCssAsset::register($this);

?>
<div class="orders-order-summary">
    
    <?= $this->render('_orderMenu', ['orderModel' => $orderModel]) ?>

    <?= $this->render('_orderView', ['orderModel' => $orderModel]) ?>
   
    <?php $canRecalcDateTime = !$orderModel->is_postponed; ?>
    <p>
        <?= Html::a('Пересчитать цену доставки*', ['delivery-price-update', 'orderId' => $orderModel->id], [
            'class' => 'btn btn-primary',
            'data' => [
                //'confirm' => 'Вы уверены?',
                'method' => 'post',
            ],
        ]) ?>

        <?php if ($canRecalcDateTime) : ?>

            <?= Html::a('Пересчитать время доставки**', ['delivery-date-time-update', 'orderId' => $orderModel->id], [
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => 'Вы уверены?',
                    'method' => 'post',
                ],
            ]) ?> 
        <?php endif; ?>
    </p>

    <p class="information bg-info">
        * цена доставки может зависеть от стоимости заказа 
        <?php if($canRecalcDateTime) : ?>
            <br />
            ** время доставки будет равным = текущеее время + время доставки из настроек
        <?php endif; ?>
    </p>

    <br />
    
    <?php if (!$orderModel->isCanceled) : ?>
        <div class="logRecordForm">

            <?php $form = ActiveForm::begin(); ?> 

                <?= $form->field($logRecordModel, 'comment')->textInput(['maxlength' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Отменить', ['name' => 'cancelButton', 'class' => 'btn btn-danger']) ?>
                    
                    <?php if ($orderModel->isNew) : ?>
                        <?= Html::submitButton('Принять', ['name' => 'acceptButton', 'class' => 'btn btn-success']) ?>
                    <?php endif; ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    <?php endif; ?>
</div>