<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\modules\picker\assets\BanknotesFormAsset;

/* @var $shiftsCourierModel \app\modules\picker\models\ShiftsCourier */
/* @var $banknotesModel \app\models\picker\models\Banknotes */

BanknotesFormAsset::register($this);

$modelName = 'Banknotes';
$sumId = 'sum';
$sumSelector = '#'.$sumId;
$this->registerJs("BanknotesFormScript.init('$modelName', '$sumSelector');", View::POS_READY);

?>
<div class="banknotesForm">
    
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($banknotesModel, 'count_5000')->textInput() ?>

        <?= $form->field($banknotesModel, 'count_1000')->textInput() ?>

        <?= $form->field($banknotesModel, 'count_500')->textInput() ?>

        <?= $form->field($banknotesModel, 'count_100')->textInput() ?>

        <?= $form->field($banknotesModel, 'count_50')->textInput() ?>

        <?= $form->field($banknotesModel, 'rest')->textInput() ?>

    <div class="form-group">
        <h3> Итого: <span id="<?=$sumId?>"></span></h3>
    </div>

    <div class="form-group">
        <?=Html::a('Назад', $previousPageUrl, ['class' => 'btn btn-primary']) ?>
        <?php 
            if ($shiftsCourierModel->isOpened) {
                $submitButtonText = 'Закрыть смену';
            } else {
                $submitButtonText = 'Далее';
            }
        ?>
        <?= Html::submitButton($submitButtonText, ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>