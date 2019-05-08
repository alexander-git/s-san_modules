<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */

$this->title = 'Пополнение';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Сейф администратора',
];
$this->params['breadcrumbs'][] = $this->title;

$typeIdsList = $replenTypesList;
$typeValuesList = $replenPurposesList;
?>
<div class="cashdesks-admincash-replenCreate">
    
    <div class="admincahsTransactForm">
        <?php $form = ActiveForm::begin(); ?>
      
        <?= $form->field($admincashTransactModel, 'type_id')->widget(Select2::className(), [
            'data' => $typeIdsList,
            'pluginOptions' => [
                'allowClear' => false,
            ],
        ])?>

        <?= $form->field($admincashTransactModel, 'type_value')->widget(Select2::className(), [
            'data' => $typeValuesList,
            'pluginOptions' => [
                'allowClear' => false,
            ],
        ])?>
        
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