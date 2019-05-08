<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use app\modules\clients\models\Client;
use app\modules\clients\assets\ActionColumnAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\clients\models\search\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Клиенты';
$this->params['breadcrumbs'][] = $this->title;

require_once __DIR__.'/_filtersHtml.php';
require_once __DIR__.'/../common/_ordersCountFilterHtml.php';

ActionColumnAsset::register($this);
?>
<div class="clients-client-index">
    
    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],

            'name',
            'fullname',
            [
                'attribute' => 'birthday',
                'filter' => $birthdayFilterHtml,
            ],
            'login',
            'email:email',
            'phone',
            'alterPhone',
            
            [
                'attribute' => 'cardnum',
                'value' => function($model, $key, $index, $column) use ($haveList) {
                    if ($model->cardnum !== null) {
                       return $haveList[1];
                    } else {
                        return $haveList[0];
                    }
                },
                'filter' => $haveList,
            ],
            [
                'attribute' => 'ordersCount',
                'filter' => $ordersCountFilterHtml,
            ],
            
            [
                'attribute' => 'state',
                'value' => 'stateName',
                'filter' => Client::getStatesArray(),
            ],

            [
                'class' => ActionColumn::className(),
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ],
    ]); ?>
</div>
