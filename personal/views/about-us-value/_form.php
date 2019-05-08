<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\SettingsPost */
/* @var $form yii\widgets\ActiveForm */

require_once __DIR__.'/../common/_ids.php';

if ($model->isNewRecord) {
    $buttonLabel = 'Создать';
    $cssClass = 'btn btn-success';
    $action = ['create'];
    $validationUrl = ['create-validate'];
} else {
    $buttonLabel = 'Обновить';
    $cssClass = 'btn btn-primary';
    $action = ['update', 'id' => $model->id];
    $validationUrl = ['update-validate', 'id' => $model->id];
}

?>

<div class="aboutUsValueForm">

    <?php $form = ActiveForm::begin([
        'id' => $createUpdateFormId,
        'enableAjaxValidation' => true,      
        'action' => $action,
        'validationUrl' => $validationUrl,
        'method' => 'post',
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($buttonLabel, ['class' => $cssClass]) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
</div>