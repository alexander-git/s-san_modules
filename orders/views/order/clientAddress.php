<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\modules\orders\assets\KladrAsset;
use app\modules\orders\assets\OrderCssAsset;
use app\modules\orders\assets\ClientAddressAsset;

/* @var $this yii\web\View */

$this->title = 'Адрес';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $orderModel->id, 'url' => ['view', 'orderId' => $orderModel->id]];
$this->params['breadcrumbs'][] = $this->title;

OrderCssAsset::register($this);
KladrAsset::register($this);
ClientAddressAsset::register($this);
$jsParams = <<<JS
    {
        'cityName' : '$cityName',
    }
JS;
$this->registerJs("ClientAddressScript.init($jsParams);", View::POS_READY);

$codeButtonHtml = Html::tag('span', 'Домофон', [
    'class' => 'input-group-addon', 
    'data-select' => 'fillCodeByHomePhoneButton',
    'style' => 'cursor : pointer',
]);
$codeInputTemplate = '<div class="input-group">{input}'.$codeButtonHtml.'</div>';


$entranceButtonsHtml = '';
$entranceButtonsHtml .= Html::tag('span', 'Первый', [
    'class' => 'input-group-addon', 
    'data-select' => 'fillEntranceButton',
    'data-value' => 'Первый',
    'style' => 'cursor : pointer',
]);

$entranceButtonsHtml .= Html::tag('span', 'Последний', [
    'class' => 'input-group-addon', 
    'data-select' => 'fillEntranceButton',
    'data-value' => 'Последний',
    'style' => 'cursor : pointer',
]);

$entranceButtonsHtml .= Html::tag('span', 'Предпоследний', [
    'class' => 'input-group-addon', 
    'data-select' => 'fillEntranceButton',
    'data-value' => 'Предпоследний',
    'style' => 'cursor : pointer',
]);

$entranceButtonsHtml .= Html::tag('span', 'Угловой', [
    'class' => 'input-group-addon', 
    'data-select' => 'fillEntranceButton',
    'data-value' => 'Угловой',
    'style' => 'cursor : pointer',
]);

$entranceInputTemplate = '<div class="input-group">{input}'.$entranceButtonsHtml.'</div>'

?>
<?= $this->render('_orderMenu', ['orderModel' => $orderModel]) ?>
<div class="orders-order-clientAddress">
    
    <div class="orderAddressForm">
        
        <?php if ($needShowCurrentAddress) : ?>
        <div class="clientAddressInfo bg-danger">
            Текущий сохранённый адресс - <?= $orderModel->address ?>
        </div>
        <?php endif; ?>
        
        <div class="clientAddressInfo bg-info">
            <?php if(!empty ($orderModel->recipient)) : ?>
                <?= $orderModel->recipient ?>, 
            <?php endif; ?>
            <?php if (!empty($orderModel->phone)) : ?>
                тел. <?= $orderModel->phone ?>, 
            <?php endif; ?>
            г. <?= $cityName ?><span data-select="address__fullInfo"></span>
        </div>

        <?php $form = ActiveForm::begin(); ?> 

            <?= $form->field($orderAddressFormModel, 'street')
                ->textInput([
                    'maxlength' => true,
                    'data-select' => 'address__streetInput',
            ]) ?>

            <?= $form->field($orderAddressFormModel, 'home')
                ->textInput([
                    'maxlength' => true,
                    'data-select' => 'address__homeInput',
            ]) ?>

            <?= $form->field($orderAddressFormModel, 'appartment')
                ->textInput([
                    'maxlength' => true,
                    'data-select' => 'address__appartmentInput',
            ]) ?>

            <?= $form->field($orderAddressFormModel, 'floor')
                ->textInput([
                    'maxlength' => true,
                    'data-select' => 'address__floorInput',
            ]) ?>

            <?= $form->field($orderAddressFormModel, 'entrance', [
                'inputTemplate' => $entranceInputTemplate
            ])->textInput([
                    'maxlength' => true,
                    'data-select' => 'address__entranceInput',
            ]) ?>

            <?= $form->field($orderAddressFormModel, 'code', [
                'inputTemplate' => $codeInputTemplate,
            ])
                ->textInput([
                    'maxlength' => true,
                    'data-select' => 'address__codeInput',
            ]) ?>

            <?= $form->field($orderAddressFormModel, 'comment')
                ->textInput([
                    'maxlength' => true,
                    'data-select' => 'address__commentInput',
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton('Дальше', ['class' => 'btn btn-success']) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>