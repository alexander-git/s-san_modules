<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\picker\models\ShiftsCourier;

/* @var $this yii\web\View */

$this->title = 'Открыть смену курьера';
$this->params['breadcrumbs'][] = [
    'url' => ['picker-index'], 
    'label' => 'Смена'
];
$this->params['breadcrumbs'][]= $this->title;
?>
<div class="picker-view-shiftCourierOpenDefault">

    <p>
        <?= Html::a('Дополнительный курьер', ['view/shift-courier-open-additional'], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <div class="shiftCourierForm">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'courier_id')->dropDownList($couriersList) ?>

        <?= $form->field($model, 'type_courier')->dropDownList(ShiftsCourier::getTypeCouriersArrayDefault()) ?>
        
        <div class="form-group">
            <?= Html::submitButton('Открыть', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>


