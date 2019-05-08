<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Обработать';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Переводы от администратора',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-admincash-transferProcess">
    
    <div class="admincahsTransactForm">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($admincashTransactModel, 'desc')->textInput(['maxlength' => true]) ?>
        
        <?= $this->render('../common/_banknotesFormItems', [
            'form' => $form,
            'banknotesModel' => $banknotesModel
        ]) ?>
        
        <div class="form-group">
            <?= Html::submitButton('Принять', ['name' => 'accept', 'value' => 'submit', 'class' => 'btn btn-success']) ?>
            <?= Html::submitButton('Отклонить', ['name' => 'reject', 'value' => 'submit', 'class' => 'btn btn-danger']) ?>
        </div>
    </div>
    
</div>