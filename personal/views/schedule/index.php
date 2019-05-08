<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'График работы';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="personal-schedule-index">
    
    <p>
        <div class="row">
            <?= Html::beginForm(['schedule'], 'get', ['class' => 'form-horizontal']) ?>
            <div class="col-lg-4">
                <?= Html::dropDownList('departmentId', null, $departmentsList, ['class' => 'form-control']) ?>
            </div>
            <div class="col-lg-4">
                <?= Html::submitButton('Выбрать',  ['class' => 'btn btn-primary']) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </p>
</div>

