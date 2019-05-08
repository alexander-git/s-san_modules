<?php

use yii\grid\GridView;
use yii\grid\SerialColumn;
use app\modules\orders\assets\ActionColumnAsset;
use app\modules\orders\assets\OrderCssAsset;

/* @var $this yii\web\View */

$this->title = $orderModel->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

ActionColumnAsset::register($this);
OrderCssAsset::register($this);

require_once __DIR__.'/_logRecordFiltersHtml.php';
?>
<div class="orders-order-view">
    
    <?= $this->render('_orderMenu', ['orderModel' => $orderModel, 'needAdditionalButtons' => true]) ?>
        
    <?= $this->render('_orderView', ['orderModel' => $orderModel]) ?>
     
    <?php if ($logRecordsCount > 0) : ?>
    
        <br />
        <h4>История</h4>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => SerialColumn::className()],
                [
                    'attribute' => 'date',
                    'format' => ['datetime', 'php:d-m-Y H:i:s'],
                    'filter' => $dateFilterHtml,
                ],
                [
                    'attribute' => 'stage_id',
                    'value' => function ($model, $key, $index, $column) use ($stagesList) {
                        return $stagesList[$model->stage_id];
                    },
                    'filter' => $stagesList,   
                ],
                'comment',
            ],
        ]); ?>    
    <?php endif; ?>
    
</div>