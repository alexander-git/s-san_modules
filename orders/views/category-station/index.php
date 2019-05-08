<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Станции категорий';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-categoryStation-index">

     
    <h3>Выберете город</h3>
    
    <p>
        <div class="row">
            <?= Html::beginForm(['view'], 'get', ['class' => 'form-horizontal']) ?>
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
