<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Настройки';
?>
<div class="picker-options-index">
   
    <p>
        <?= Html::a('Список настроек', ['option-index'], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <h3> Настройки для департаментов</h3>
    
    <p>
        <div class="row">
            <?= Html::beginForm(['val-index'], 'get', ['class' => 'form-horizontal']) ?>
            <div class="col-lg-4">
                <?= Html::dropDownList('departmentId', null, $departmentsList, ['class' => 'form-control']) ?>
            </div>
            <div class="col-lg-4">
                <?= Html::submitButton('Перейти',  ['class' => 'btn btn-primary']) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </p>
</div>

