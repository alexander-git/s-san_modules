<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\cashdesks\models\AdmincashTransact;

/* @var $this yii\web\View */

$this->title = 'Обновить';
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
        
        
        <?= $form->field($admincashTransactModel, 'state')->dropDownList(AdmincashTransact::getStatesArray()) ?>
        
        <?= $this->render('../common/_banknotesFormItems', [
            'form' => $form,
            'banknotesModel' => $banknotesModel
        ]) ?>
        
        <div class="form-group">
            <?= Html::submitButton('Обновить', ['class' => 'btn btn-success', 'name' => 'submitButton']) ?>        
        </div>
    </div>
    
</div>