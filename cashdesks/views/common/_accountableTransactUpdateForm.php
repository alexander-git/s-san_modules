<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

?>
<div class="accountableTransactForm">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($needUsersList) : ?> 

        <?= $form->field($accountableTransactModel, 'user_id')->widget(Select2::className(), [
            'data' => $usersList,
            'pluginOptions' => [
                'allowClear' => false,
            ],
        ]) ?>

    <?php endif; ?>

    <?= $form->field($accountableTransactModel, 'sum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($accountableTransactModel, 'desc')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Обновить', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
