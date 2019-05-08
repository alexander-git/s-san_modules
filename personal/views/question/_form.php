<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
//use kartik\select2\Select2;
use app\modules\personal\models\Question;


/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Question */
/* @var $form yii\widgets\ActiveForm */

if (!isset($needDate)) {
    $needDate = false;
}

if (!isset($needHistoryText)) {
    $needHistoryText = false;
}

$isUpdate = !$questionModel->isNewRecord;

if (!isset($buttonLabel)) {
    if (!$isUpdate) {
        $buttonLabel = 'Создать';
    } else {
        $buttonLabel = 'Выполнить'; 
    }    
}

if (!$isUpdate) {
    $cssClass = 'btn btn-success';
} else {
    $cssClass = 'btn btn-primary';
}

$lists = $this->context->getListsForRender();

?>

<div class="questionForm">
        
    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($questionModel, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($questionModel, 'post_id')->dropDownList($lists['settingsPostsList']) ?>

        <?= $form->field($questionModel, 'birthday')->widget(DatePicker::className(), [
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
            ],
        ])?>
        
        <?= $form->field($questionModel, 'city')->dropDownList($lists['citiesList']) ?>

        <?= $form->field($questionModel, 'address')->textInput(['maxlength' => true]) ?>

        <?= $form->field($questionModel, 'phone')->textInput(['maxlength' => true]) ?>
        
        <?= $form->field($questionModel, 'work_time')->inline(false)->checkboxList(Question::getWorkTimeArray()) ?>

        <?= $form->field($questionModel, 'med_book')->dropDownList($lists['haveList']) ?>

        <?= $form->field($questionModel, 'children')->dropDownList($lists['haveList']) ?>

        <?= $form->field($questionModel, 'smoking')->dropDownList($lists['yesNoList']) ?>

        <?= $form->field($questionModel, 'about_us_id')->dropdownList($lists['aboutUsValuesList']) ?>

        <?= $form->field($questionModel, 'experience')->textarea(['rows' => 6]) ?>

        <?= $form->field($questionModel, 'hobby')->textarea(['rows' => 6]) ?>

        <?php if ($needDate) : ?>
            <?php 
                $field =  $form->field($questionModel, 'date');
                if (isset($dateLabel)) {
                    $field->label($dateLabel); 
                }
            ?>
            <?= $field->widget(DatePicker::className(), [
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy',
                    ],
            ])?>
        <?php endif; ?>
    
        <?php if ($needHistoryText) : ?>
            <?= $form->field($questionHistoryModel, 'text')->textInput(['maxlength' => true]) ?>
        <?php endif; ?>
        
        
        <div class="form-group">
            <?= Html::submitButton($buttonLabel, ['class' => $cssClass]) ?>
        </div>

    <?php ActiveForm::end(); ?>
        
</div>
