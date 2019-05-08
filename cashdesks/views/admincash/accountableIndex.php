<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = 'Касса "Под отчёт"';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Сейф администратора',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-admincash-accountableIndex">
    
    <p>
        <?= Html::a('Пополнить', ['accountable-replen-create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Изъять', ['accountable-return-create'], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <br />
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'depart_id',
                'value' => $model->departmentName,
            ],
            'current',
            'max_sum',
        ],
    ]) ?>

</div>