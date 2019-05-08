<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Расход';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Сейф администратора',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-admincash-expenseTypeSelect">
    
    <p>
        Выберете тип расхода.
    </p>
    <div class="row">
        <?= Html::beginForm(['expense-create'], 'get', ['class' => 'form-horizontal']) ?>
        <div class="col-lg-4">
            <?= Html::dropDownList('id', null, $expenseTypesList, ['class' => 'form-control']) ?>
        </div>
        <div class="col-lg-4">
            <?= Html::submitButton('Далее',  ['class' => 'btn btn-primary']) ?>
        </div>
        <?= Html::endForm() ?>
    </div>
    
</div>