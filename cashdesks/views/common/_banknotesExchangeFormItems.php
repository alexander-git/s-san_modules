<?php

use yii\web\View;
use kartik\touchspin\TouchSpin;
use app\modules\cashdesks\assets\BanknotesFormAsset;

/* @var $form \yii\widgets\ActiveForm */
/* @var $banknotesModel \app\models\picker\models\Banknotes */

BanknotesFormAsset::register($this);

$modelName = 'Banknotes';
$sumId = 'sum';
$sumSelector = '#'.$sumId;
$this->registerJs("BanknotesFormScript.init('$modelName', '$sumSelector');", View::POS_READY);

$touchspinConfig = [
    'pluginOptions' => [
        'step' => 1,
        'min' => -100000000,
        'max' => 100000000,
        //'buttonup_class' => 'btn btn-default', 
        //'buttondown_class' => 'btn btn-default', 
        //'buttonup_txt' => '<i class="glyphicon glyphicon-plus-sign"></i>', 
        //'buttondown_txt' => '<i class="glyphicon glyphicon-minus-sign"></i>',
        'verticalbuttons' => true,
        'verticalupclass' => 'glyphicon glyphicon-plus',
        'verticaldownclass' => 'glyphicon glyphicon-minus',
    ],
];

?>

<?= $form->field($banknotesModel, 'count_5000')->widget(TouchSpin::className(), $touchspinConfig) ?>

<?= $form->field($banknotesModel, 'count_1000')->widget(TouchSpin::className(), $touchspinConfig) ?>

<?= $form->field($banknotesModel, 'count_500')->widget(TouchSpin::className(), $touchspinConfig) ?>

<?= $form->field($banknotesModel, 'count_100')->widget(TouchSpin::className(), $touchspinConfig) ?>

<?= $form->field($banknotesModel, 'count_50')->widget(TouchSpin::className(), $touchspinConfig) ?>

<?= $form->field($banknotesModel, 'rest')->widget(TouchSpin::className(), $touchspinConfig) ?>

<div class="form-group">
    <h3> Итого: <span id="<?=$sumId?>"></span></h3>
</div>

<?php if ($banknotesModel->hasErrors('exchangeSummary')) : ?>
    <div class="alert alert-danger">
        <?= $banknotesModel->getFirstError('exchangeSummary'); ?>
    </div>
<?php endif; ?>