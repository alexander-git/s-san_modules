<?php

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Question */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вернуть в начальное состояние';
$this->params['breadcrumbs'][] = [
    'label' => 'Анкеты', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = [
    'label' => $questionModel->name,
    'url' => ['view', 'id' => $questionModel->id],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="personal-question-returnToCreateState">

   <div class="questionHistoryForm">
        
        <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($questionHistoryModel, 'text')->textInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Выполнить', ['class' => 'btn btn-primary']) ?>
            </div>

        <?php ActiveForm::end(); ?>
        
    </div>
            
</div>
