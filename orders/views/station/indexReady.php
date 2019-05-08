<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use app\modules\orders\assets\ActionColumnAsset;

/* @var $this yii\web\View */

$this->title = 'Готовые';
$this->params['breadcrumbs'][] = [
    'label' => 'Станции ('.$cityName.')', 
    'url' => ['index', 'cityId' => $cityId]
];
$this->params['breadcrumbs'][] = $this->title; 

ActionColumnAsset::register($this);

require_once __DIR__.'/../order/_orderFiltersHtml.php';
?>
<div class="orders-station-indexReady">
    
    <p>
        <?= Html::a('В Работе', ['station-pick', 'cityId' => $cityId], ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Готовые', ['index-ready', 'cityId' => $cityId], ['class' => 'btn btn-success']); ?>
    </p>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],
            'id',
            'order_num',
            [
                'attribute' => 'stage_id',
                'value' => function ($model, $key, $index, $column) use ($stagesList) {
                    return $stagesList[$model->stage_id];
                },
                'filter' => $stagesList,   
            ],
            [
                'attribute' => 'delivery_date',
                'filter' => $deliveryDateFilterHtml,
            ],
            [
                'attribute' => 'delivery_time',
                'filter' => $deliveryTimeFilterHtml,
            ],
            [
                'class' => ActionColumn::className(),
                'contentOptions' => ['class' => 'actionColumn'],
                'template' => '{printCheckCourier} {printCheckClient}',
                'buttons' => [
                    'printCheckCourier' => function($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-print']);
                        return Html::a($icon, $url, [
                            'class' => 'text-primary',
                            'title' => 'Печать чека курьера',
                            'target' => '_blank',
                        ]);
                    },         
                    'printCheckClient' => function($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-print']);
                        return Html::a($icon, $url, [
                            'class' => 'text-success',
                            'title' => 'Печать чека клиента',
                            'target' => '_blank',
                        ]);
                    }, 
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    $url = null;
                    if ($action === 'printCheckCourier') {
                        $url = Url::to(['check/print-check-courier', 'orderId' => $model->id]);
                    } else if ($action === 'printCheckClient') {
                        $url = Url::to(['check/print-check-client', 'orderId' => $model->id]);
                    }
   
                    return $url;
                },
            ],
        ],
    ]); ?>
     
</div>
