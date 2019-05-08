<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */

$this->title = 'Выдать деньги пользователю';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Сейф администратора',
];
$this->params['breadcrumbs'][] = [
    'url' => ['acctab-index'],
    'label' => 'Под отчёт',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-admincash-acctabUserCreate">
    
    <div class="admincahsTransactForm">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($admincashTransactModel, 'user_id')->widget(Select2::className(), [
            'data' => $usersList,
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