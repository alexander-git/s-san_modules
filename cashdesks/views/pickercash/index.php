<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = 'Касса комплектовщика';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-pickercash-index">    
    <p>
        <?= Html::a('Приём денег от курьра', ['replen-courier-create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Учёт самовывоза', ['replen-pickup-create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Перевод в сейф администратора', ['transfer-to-admincash'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Размен купюр', ['exchange'], ['class' => 'btn btn-primary']) ?> 
        <?= Html::a('История операций', ['history'], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <br />
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'depart_id',
                'value' => $model->departmentName,
            ],
            'banknotes.count_5000',
            'banknotes.count_1000',
            'banknotes.count_500',
            'banknotes.count_100',
            'banknotes.count_50',
            'banknotes.rest',
            'banknotes.sum',
        ],
    ]) ?>
       
</div>
