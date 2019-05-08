<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\modules\orders\assets\OrderCssAsset;
use app\modules\orders\assets\ClientNameAsset;

/* @var $this yii\web\View */

$this->title = 'Имя клиента';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $orderModel->id, 'url' => ['view', 'orderId' => $orderModel->id]];
$this->params['breadcrumbs'][] = $this->title;

OrderCssAsset::register($this);
ClientNameAsset::register($this);

$this->registerJs("ClientNameScript.init();", View::POS_READY);
$isClientFound = $clientModel !== null; 

if ($isClientFound) {
    $clientButtonHtml = Html::tag('span', 'Клиент', [
        'class' => 'input-group-addon', 
        'data-select' => 'fillRecipientButton',
        'style' => 'cursor : pointer',
    ]);
    $recipientInputTemplate = '<div class="input-group">{input}'.$clientButtonHtml.'</div>';
} else {
    $recipientInputTemplate = '{input}';
}
 
?>
<div class="orders-order-clientName">

    <?= $this->render('_orderMenu', ['orderModel' => $orderModel]) ?>

    <?php if ($isClientFound) : ?>
        <p class="bg-info clientNameInfo">
            Клиент - <span data-select="clientName"><?=$clientModel->name ?></span><br />
            Статус - <?=$clientModel->stateName ?> 
        </p>
    <?php else : ?>
        <p class="bg-info clientNameInfo">
            Клиент не найден.
        </p>
    <?php endif; ?>

    <div class="orderForm">

        <?php $form = ActiveForm::begin(); ?> 

        <?= $form->field($orderModel, 'recipient', [
            'inputTemplate' => $recipientInputTemplate,
        ])
        ->textInput([
            'maxlength' => true,
            'data-select' => 'recipientInput',
        ])  ?>

        <div class="form-group">
            <?= Html::submitButton('Дальше', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
        
</div>
