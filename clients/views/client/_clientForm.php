<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use app\modules\clients\models\Client;

/* @var $this yii\web\View */
/* @var $model app\modules\clients\models\Client */
/* @var $form yii\widgets\ActiveForm */

if ($model->isNewRecord) {
    $buttonLabel = 'Создать';
    $cssClass = 'btn btn-success';
} else {
    $buttonLabel = 'Обновить';
    $cssClass = 'btn btn-primary';
}

?>

<div class="clientForm">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'birthday')->widget(DatePicker::className(),[
        'pluginOptions' => [
            'format' => 'dd-mm-yyyy',
        ],
    ]) ?>

    <?= $form->field($model, 'login')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'newPassword')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'newPasswordRepeat')->passwordInput(['maxlength' => true]) ?>
   
    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'alterPhone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'state')->dropDownList(Client::getStatesArray())?>

    <div class="form-group">
        <?= Html::submitButton($buttonLabel, ['class' => $cssClass]) ?>
    </div>
                    
    <?php ActiveForm::end(); ?>

</div>
