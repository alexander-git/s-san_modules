<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\date\DatePicker;
use app\modules\orders\assets\OrderCssAsset;
use app\modules\orders\assets\InfoAsset;


/* @var $this yii\web\View */

$this->title = 'Информация';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $orderModel->id, 'url' => ['view', 'orderId' => $orderModel->id]];
$this->params['breadcrumbs'][] = $this->title;

OrderCssAsset::register($this);
InfoAsset::register($this);

$jsParams = <<<JS
    {
    }
JS;


$this->registerJs("InfoScript.init($jsParams);", View::POS_READY);

$returnSumButtons = '';
$sumValues = [5, 10, 50, 100, 1000, 5000];
foreach ($sumValues as $sumValue) {
    $returnSumButtons .=  Html::tag('span', $sumValue, [
        'class' => 'input-group-addon', 
        'data-select' => 'fillReturnSumButton',
        'data-value' => $sumValue,
        'style' => 'cursor : pointer',
    ]);
}
$returnSumButtons .=  Html::tag('span', 'Ровно', [
    'class' => 'input-group-addon', 
    'data-select' => 'fillReturnSumButton',
    'data-value' => 0,
    'style' => 'cursor : pointer',
]);

$returnSumInputTemplate = '<div class="input-group">{input}'.$returnSumButtons.'</div>';

?>
<div class="orders-order-info">
    
    <?= $this->render('_orderMenu', ['orderModel' => $orderModel]) ?>

    <div class="orderForm">

        <?php $form = ActiveForm::begin(); ?> 
            <?= $form->field($orderModel, 'person_num')->textInput([
                    'maxlength' => true,
            ]) ?>
        
        
            <?= $form->field($orderModel, 'return_sum', [
                'inputTemplate' => $returnSumInputTemplate,
            ])->textInput([
                    'maxlength' => true,
                    'data-select' => 'returnSumInput',
            ]) ?>

            <?= $form->field($orderModel, 'payment_type')->dropDownList($paymentTypesList) ?>
        
            <br />

            <?= $form->field($orderModel, 'is_postponed')->checkbox([
                'data-select' => 'isPostponedInput'
            ])?>

            <div data-select="postponedOrderInfo">

                <div class="form-group">
                    <div class="btn-group">
                        <?php foreach ($dayButtonItems as $text => $value) : ?>
                            <?= Html::button($text, [
                                'data-value' => $value, 
                                'data-select' => 'fillDeliveryDateButton',
                                'class' => 'btn btn-default'
                            ]) ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?= $form->field($orderModel, 'delivery_date')->widget(DatePicker::className(), [
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy',
                    ],
                    'options' => [
                        'data-select' => 'deliveryDateInput',
                    ],
                ])?>

                <br />

                <?php 
                /*
                <?php if (!empty($minPossibleDeliveryTime) && !empty($maxPossibleDeliveryTime)) : ?>
                <p class="bg-info possibleDeliveryTimeInfo">
                    Возможное время доставки: <?= $minPossibleDeliveryTime ?> - <?= $maxPossibleDeliveryTime ?>
                </p>
                <?php endif; ?>
                */
                ?>
                
                <?php if (count($timeHourButtonItems) > 0) : ?>
                    <div class="form-group">
                        Часы <br />
                        <div class="btn-group">

                            <?php foreach ($timeHourButtonItems as $text => $value) : ?>
                                <?= Html::button($text, [
                                    'data-value' => $value, 
                                    'data-select' => 'fillHourDeliveryTimeButton',
                                    'class' => 'btn btn-default'
                                ]) ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (count($timeMinuteButtonItems) > 0) : ?>
                    <div class="form-group">
                        Минуты <br />
                        <div class="btn-group">
                            <?php foreach ($timeMinuteButtonItems as $text => $value) : ?>
                                <?= Html::button($text, [
                                    'data-value' => $value, 
                                    'data-select' => 'fillMinuteDeliveryTimeButton',
                                    'class' => 'btn btn-default'
                                ]) ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?= $form->field($orderModel, 'delivery_time')->widget(MaskedInput::className(), [
                        'mask' => '99:99',
                        'options' => [
                            'data-select' => 'deliveryTimeInput',
                            'class' => 'form-control',
                        ],
                ]) ?>

            </div>

            <div class="form-group">
                <?= Html::submitButton('Далее', ['class' => 'btn btn-success']) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>