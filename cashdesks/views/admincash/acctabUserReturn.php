<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Возврат денег';
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
<div class="cashdesks-admincash-acctabUserReturn">
    
    <div class="alert alert-info">
        Пользователь: <?= $admincashTransactModel->userName ?><br /> 
        Сумма: <?= $admincashTransactModel->banknotes->sum ?> руб.
    </div>
    
    <div class="admincahsTransactForm">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($admincashTransactModel, 'desc')->textInput(['maxlength' => true]) ?>
        
        <div class="form-group">
            <?= Html::submitButton('Выполнить', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>
    
</div>