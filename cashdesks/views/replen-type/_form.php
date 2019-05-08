<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\cashdesks\models\ReplenType */
/* @var $form yii\widgets\ActiveForm */
if ($model->isNewRecord) {
    $buttonLabel = 'Создать';
    $cssClass = 'btn btn-success';
} else {
    $buttonLabel = 'Обновить';
    $cssClass = 'btn btn-primary';
}

?>
<div class="replenTypeForm">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($buttonLabel, ['class' => $cssClass]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
