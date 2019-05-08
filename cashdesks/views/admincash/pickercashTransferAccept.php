<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Приянть';

$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Сейф администратора',
];
$this->params['breadcrumbs'][] = [
    'url' => ['pickercash-transfer-index'],
    'label' => 'Переводы из касссы комплектовщика',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-admincash-pickercashTransferAccept">
    
    <?= $this->render('_transferFromPickercashView', [
        'model' => $pickercashTransactModel,
        'showBeforeAcceptReject' => true,
    ]); ?>          
    
    <div class="pickercashTransactForm">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($pickercashTransactModel, 'desc')->textInput(['maxlength' => true]) ?>
        
        <div class="form-group">
            <?= Html::submitButton('Приянть', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>
    
</div>