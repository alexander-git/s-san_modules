<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;

/* @var $this yii\web\View */

$this->title = 'Виды пополнений';
$this->params['breadcrumbs'] []= [
    'url' => ['service/index'],
    'label' => 'Управление'
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-replenType-index">
    
    <p>
        <?= Html::a('Cоздать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],
            'name',
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
    
</div>
