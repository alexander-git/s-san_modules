<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use app\modules\personal\models\Card;

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Card */
/* @var $form yii\widgets\ActiveForm */

$isUpdate = !$model->isNewRecord;
if (!$isUpdate) {
    $buttonLabel = 'Создать';
    $cssClass = 'btn btn-success';
} else {
    $buttonLabel = 'Обновить';
    $cssClass = 'btn btn-primary';
}

$lists = $this->context->getListsForRender();

?>

<div class="cardForm">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'post_id')->dropDownList($lists['settingsPostsList']) ?>

    <?= $form->field($model, 'depart_id')->dropDownList($lists['departmentsList']) ?>

    <?= $form->field($model, 'birthday')->widget(DatePicker::className(),[
        'pluginOptions' => [
            'format' => 'dd-mm-yyyy',
        ],
    ]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'rate')->textInput() ?>

    <?= $form->field($model, 'med_book')->dropDownList($lists['haveList']) ?>
    
    <?= $form->field($model, 'student')->dropDownList($lists['yesNoList']) ?>

    <?= $form->field($model, 'docs_ok')->dropDownList($lists['yesNoList']) ?>

    <?= $form->field($model, 'date_employment')->widget(DatePicker::className(),[
        'pluginOptions' => [
            'format' => 'dd-mm-yyyy',
        ],
    ]) ?>

    <?= $form->field($model, 'date_obt_input')->widget(DatePicker::className(),[
        'pluginOptions' => [
            'format' => 'dd-mm-yyyy',
        ],
    ]) ?>

    <?= $form->field($model, 'date_obt_first')->widget(DatePicker::className(),[
        'pluginOptions' => [
            'format' => 'dd-mm-yyyy',
        ],
    ]) ?>

    <?= $form->field($model, 'state')->dropDownList(Card::getStatesArray()) ?>

    <div class="form-group">
        <?= Html::submitButton($buttonLabel, ['class' => $cssClass]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
