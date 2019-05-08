<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\modules\orders\assets\OrderCssAsset;
use app\modules\orders\assets\ClientPhoneAsset;

/* @var $this yii\web\View */

$this->title = 'Телефон';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $orderModel->id, 'url' => ['view', 'orderId' => $orderModel->id]];
$this->params['breadcrumbs'][] = $this->title;

OrderCssAsset::register($this);
ClientPhoneAsset::register($this);

$this->registerJs("ClientPhoneScript.init();", View::POS_READY);

$isClientFound = $clientModel !== null; 
$isAlterPhoneSet = $isClientFound && !empty($clientModel->alterPhone); 
if ($isClientFound) {
    $buttonHtml = Html::tag('span', 'Телефон', [
        'class' => 'input-group-addon', 
        'data-select' => 'fillPhoneButton',
        'style' => 'cursor : pointer',
    ]);
    
    $phoneInputTemplate = '<div class="input-group">{input}'.$buttonHtml.'</div>';
} else {
    $phoneInputTemplate = '{input}';
}

if ($isAlterPhoneSet) {
    $buttonHtml = Html::tag('span', 'Дополнительный телефлон', [
        'class' => 'input-group-addon', 
        'data-select' => 'fillAlterPhoneButton',
        'style' => 'cursor : pointer',
    ]);
    $alterPhoneInputTemplate = '<div class="input-group">{input}'.$buttonHtml.'</div>';
} else {
    $alterPhoneInputTemplate = '{input}';
}
 
?>
<div class="orders-order-clientPhone">

    <?= $this->render('_orderMenu', ['orderModel' => $orderModel]) ?>

    <?php if ($isClientFound) : ?>
        <p class="bg-info clientNameInfo">
            Клиент - <?=$clientModel->name ?><br />
            Статус - <?=$clientModel->stateName ?><br />
            Основной телефон - <span data-select="clientPhone"><?=$clientModel->phone ?></span><br />
            <?php if (!empty($isAlterPhoneSet)) : ?> 
                Дополнительный телефон - <span data-select="clientAlterPhone"><?=$clientModel->alterPhone ?></span>
            <?php endif; ?>
        </p>
    <?php else : ?>
        <p class="bg-info clientNameInfo">
            Клиент не найден.
        </p>
    <?php endif; ?>

    <div class="orderForm">

        <?php $form = ActiveForm::begin(); ?> 

            <?= $form->field($orderModel, 'phone', [
                'inputTemplate' => $phoneInputTemplate,
            ])
            ->textInput([
                'maxlength' => true,
                'data-select' => 'phoneInput',
            ])  ?>


            <?= $form->field($orderModel, 'alter_phone', [
                'inputTemplate' => $alterPhoneInputTemplate,
            ])
            ->textInput([
                'maxlength' => true,
                'data-select' => 'alterPhoneInput',
            ])  ?>

            <div class="form-group">
                <?= Html::submitButton('Дальше', ['class' => 'btn btn-success']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>