<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use app\modules\clients\assets\ActionColumnAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\clients\models\search\AddressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Адреса';
$this->params['breadcrumbs'][] = $this->title;

require_once __DIR__.'/../common/_ordersCountFilterHtml.php';

ActionColumnAsset::register($this);
?>
<div class="clients-address-index">
    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],
            [
                'attribute' => 'cityId',
                'value' => 'cityName',
                'filter' => $citiesList,
            ],
            'street',
            'home',
            'appart',
            'floor',
            'name',
            [
                'attribute' => 'ordersCount',
                'filter' => $ordersCountFilterHtml,
            ],
            [
                'class' => ActionColumn::className(),
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ],
    ]); ?>
</div>
