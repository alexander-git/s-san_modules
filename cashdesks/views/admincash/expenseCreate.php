<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */

$this->title = $expenseTypeModel->name;
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Сейф администратора',
];
$this->params['breadcrumbs'][] = [
    'url' => ['expense-type-select'],
    'label' => 'Расход',
];
$this->params['breadcrumbs'][] = $this->title;
    
?>
<div class="cashdesks-admincash-expenseCreate">
    
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
        
        <?php if ($needTypeValueText) : ?>
            <?= $form->field($admincashTransactModel, 'type_value')->textInput(['maxlength' => true]) ?> 
        <?php endif; ?>
        
        <?php if ($needExpenseTypeItemsList) : ?>
            <?= $form->field($admincashTransactModel, 'type_value')->widget(Select2::className(), [
                'data' => $expenseTypeItemsList,
                'pluginOptions' => [
                    'allowClear' => false,
                ],
            ])?>
        <?php endif; ?>
        
        <?= $form->field($admincashTransactModel, 'desc')->textInput(['maxlength' => true]) ?>

        
        <?= $this->render('../common/_banknotesFormItems', [
            'form' => $form,
            'banknotesModel' => $banknotesModel
        ]) ?>
        
        <div class="form-group">
            <?= Html::submitButton('Выполнить', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>
    
</div>