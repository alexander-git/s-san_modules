<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\personal\models\Vacancy;

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Vacancy */
/* @var $form yii\widgets\ActiveForm */

$isUpdate = !$model->isNewRecord;
if (!$isUpdate) {
    $buttonLabel = 'Создать';
    $cssClass = 'btn btn-success';
} else {
    $buttonLabel = 'Обновить';
    $cssClass = 'btn btn-primary';
}

?>
<div class="vacancyForm">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'post_id')->dropDownList($settingsPostsList) ?>

    <?= $form->field($model, 'depart_id')->dropDownList($departmentsList) ?>
    
    <?php if ($isUpdate) : ?>
        <?= $form->field($model, 'state')->dropDownList(Vacancy::getStatesArray()) ?>
    <?php endif; ?>
    
    <div class="form-group">
        <?= Html::submitButton($buttonLabel, ['class' => $cssClass]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
