<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

?>    
<div class="alert alert-info">
    Тип операции: <?=$admincashTransactModel->typeName ?> <br />
    Состоянии операции: <?=$admincashTransactModel->stateName ?>
</div>

<div class="admincahsTransactForm">
    <?php $form = ActiveForm::begin(); ?>

    <?php if ($needUsersList) : ?>
        <?= $form->field($admincashTransactModel, 'user_id')->widget(Select2::className(), [
            'data' => $usersList,
            'pluginOptions' => [
                'allowClear' => !$userIdRequired,
            ],
        ])?>
    <?php endif; ?>


    <?php if ($needTypeIdsList) : ?>
        <?= $form->field($admincashTransactModel, 'type_id')->widget(Select2::className(), [
            'data' => $typeIdsList,
            'pluginOptions' => [
                'allowClear' => false,
            ],
        ])?>
    <?php endif; ?>

    <?php if ($needTypeValuesList) : ?>
        <?= $form->field($admincashTransactModel, 'type_value')->widget(Select2::className(), [
                'data' => $typeValuesList,
                'pluginOptions' => [
                    'allowClear' => false,
                ],
        ])?>
    <?php endif; ?>

    <?php if ($needTypeValueText) : ?>
         <?= $form->field($admincashTransactModel, 'type_value')->textInput(['maxlength' => true]) ?> 
    <?php endif; ?>

    <?php if ($needStatesList) : ?>
        <?= $form->field($admincashTransactModel, 'state')->dropDownList($statesList) ?>
    <?php endif; ?>

    <?= $form->field($admincashTransactModel, 'desc')->textInput(['maxlength' => true]) ?>

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
    