<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Card */

$this->title = 'Редактировать мед. книжку';

$this->params['breadcrumbs'][] = [
    'label' => 'Карточки сотрудников', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = [
    'label' => $cardModel->name, 
    'url' => ['view', 'id' => $cardModel->id]
];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="personal-card-updateMedbook">

    <div class="medbookForm">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($medbookModel, 'date_sanmin')->widget(DatePicker::className(),[
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
            ],
        ]) ?>

        <?= $form->field($medbookModel, 'date_sanmin_end')->widget(DatePicker::className(),[
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
            ],
        ]) ?>

        <?= $form->field($medbookModel, 'date_exam')->widget(DatePicker::className(),[
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
            ],
        ]) ?>
        
        <?= $form->field($medbookModel, 'date_exam_end')->widget(DatePicker::className(),[
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
            ],
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
