<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = 'Сейф администратора';
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="cashdesks-admincash-index">    
    
    <?php if ($createdTransfersToAdmincashCount > 0) : ?>
        <p class="alert alert-danger">
           Есть необработнные переводы из кассы комплектовщика
            <?= Html::a(
                "($createdTransfersToAdmincashCount)",
                ['pickercash-transfer-index'],
                ['class' => 'alert-link']
           )?>
        </p>
    <?php endif; ?>
    
    <p>
        <?= Html::a('Пополнение', ['replen-create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Расход', ['expense-type-select'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Корректировка', ['change'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Размен', ['exchange'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Под отчёт', ['acctab-index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Касса "под отчёт"', ['accountable-index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Переводы от комплектовщиков', ['pickercash-transfer-index'], ['class' => 'btn btn-primary']) ?>
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
