<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

?>
    
<div class="alert alert-info">
    Тип операции: <?=$pickercashTransactModel->typeName ?> <br />
    Состоянии операции: <?=$pickercashTransactModel->stateName ?> <br />
</div>

<div class="pickercashTransactForm">
    <?php $form = ActiveForm::begin(); ?>

    <?php if ($needUsersList) : ?>
        <?= $form->field($pickercashTransactModel, 'user_id')->widget(Select2::className(), [
            'data' => $usersList,
            'pluginOptions' => [
                'allowClear' => false,
            ],
        ])?>
    <?php endif; ?>

   <?= $form->field($pickercashTransactModel, 'desc')->textInput(['maxlength' => true]) ?>

    <?php if ($needBanknotes) : ?>

        <?= $this->render('_banknotesFormItems', [
            'form' => $form,
            'banknotesModel' => $banknotesModel
        ]) ?>

    <?php endif; ?>

    <?php if ($needBanknotesExchangeForm) : ?>         
        <?= $this->render('_banknotesExchangeFormItems', [
            'form' => $form,
            'banknotesModel' => $banknotesModel
        ]) ?>
    <?php endif; ?>


    <div class="form-group">
        <?= Html::submitButton('Обновить', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>
