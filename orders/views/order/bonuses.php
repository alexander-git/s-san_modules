<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use app\modules\orders\assets\OrderCssAsset;
use app\modules\orders\assets\BonusesAsset;

/* @var $this yii\web\View */

$this->title = 'Бонусы';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $orderModel->id, 'url' => ['view', 'orderId' => $orderModel->id]];
$this->params['breadcrumbs'][] = $this->title;

OrderCssAsset::register($this);
BonusesAsset::register($this);

$sendCodeUrl = Url::to(['send-promo-code', 'orderId' => $orderModel->id]);
$payByBonusessUrl = Url::to(['pay-by-bonuses', 'orderId' => $orderModel->id]);

$isOrderEmpty = count($orderModel->orderItems) === 0;
$hasBonuscard = $bonuscardModel !== null;

if ($hasBonuscard) {
    $hasBonuscardJsValue = 'true';
    $bonusesJsValue = $bonuscardModel->bonuses;
} else {
    $hasBonuscardJsValue = 'false'; 
    $bonusesJsValue = 0;
}

$jsParams = <<<JS
    {
        'hasBonuscard' : $hasBonuscardJsValue,
        'totalPrice' : $orderModel->total_price,
        'bonuses' : $bonusesJsValue,
        'sendCodeUrl' : '$sendCodeUrl', 
        'payByBonusesUrl' : '$payByBonusessUrl',
    }
JS;

$this->registerJs("BonusesScript.init($jsParams);", View::POS_READY);


if ($hasBonuscard) {
    $taxButtonHtml = Html::tag('span', 'Все', [
        'class' => 'input-group-addon', 
        'data-select' => 'fillTaxByAllBonusesButton',
        'style' => 'cursor : pointer',
    ]);
    $taxInputTemplate = '<div class="input-group">{input}'.$taxButtonHtml.'</div>';
} else {
    $taxInputTemplate = '{input}';
}
?>
<div class="orders-order-bonuses">

    <?= $this->render('_orderMenu', ['orderModel' => $orderModel]) ?>

    <div class="orderForm">

        <div class="clientBonusesInfo bg-info">
            Суммарная цена заказа - <?= $orderModel->total_price ?><br />
            <?php if ($hasBonuscard) : ?>
                Тип бонусного счёта - <?= $bonuscardModel->typeName ?><br />
                Потраченные деньги - <?= $bonuscardModel->moneyquan ?><br />
                Бонусов на счету - <span data-select="bonusesValue"><?= $bonuscardModel->bonuses ?></span>
            <?php else : ?>
                Нет бонусоного счёта.
            <?php endif; ?>
        </div>

        <?php $form = ActiveForm::begin(); ?> 
   
            <?php if ($hasBonuscard && !$isOrderEmpty) : ?>
                <?= $form->field($orderModel, 'tax', [
                    'inputTemplate' => $taxInputTemplate
                ])->textInput([
                        'maxlength' => true,
                        'data-select' => 'taxInput',
                ]) ?>

                <div class="form-group">
                    <?= Html::button('Оплатить', [
                        'class' => 'btn btn-success',
                        'data-select' => 'payByBonusesButton',
                    ]) ?>
                </div>
            <?php endif; ?>
            
            <?php 
            /*
            // Промокоды.
            <?php if (!$isOrderEmpty) : ?>

                <div class="form-group">
                    <label class="control-label" for="codeInput"> 
                        Код
                    </label>

                    <?= Html::textInput('code', '', [
                        'id' => 'codeInput',
                        'class' => 'form-control',
                        'data-select' => 'codeInput'
                    ]) ?>
                </div>

                <div class="form-group">
                    <?= Html::button('Отправить', [
                        'class' => 'btn btn-success',
                        'data-select' => 'sendCodeButton',
                    ]) ?>
                </div>
            <?php endif; ?>

            */
            ?>
             

            <div class="form-group">
                <?= Html::a('Дальше', ['info', 'orderId' => $orderModel->id], ['class' => 'btn btn-success']) ?>
            </div>
           
        <?php ActiveForm::end(); ?>
    </div>
</div>