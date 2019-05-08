<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Основные Настройки';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['settings/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-option-index">
   
    <p>
        <?= Html::a('Список', ['option-index'], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <h3>Настройки для города</h3>
    
    <p>
        <div class="row">
            <?= Html::beginForm(['val-index'], 'get', ['class' => 'form-horizontal']) ?>
            <div class="col-lg-4">
                <?= Html::dropDownList('cityId', null, $citiesList, ['class' => 'form-control']) ?>
            </div>
            <div class="col-lg-4">
                <?= Html::submitButton('Перейти',  ['class' => 'btn btn-primary']) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </p>
    
</div>

