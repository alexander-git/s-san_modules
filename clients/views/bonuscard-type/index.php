<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use app\modules\clients\assets\ActionColumnAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\clients\models\search\BonuscardTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Типы бонусных карт';
$this->params['breadcrumbs'][] = $this->title;

require_once __DIR__.'/_filtersHtml.php';

ActionColumnAsset::register($this);
?>
<div class="clients-bonuscardType-index">

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],
            
            'name',
            [
                'attribute' => 'discount',
                'filter' => $discountFilterHtml,
            ],
                
            [
                'attribute' => 'bonusquan',
                'filter' => $bonusquanFilterHtml,
            ],
            [
                'attribute' => 'minmoney',
                'filter' => $minmoneyFilterHtml,
            ],
            [
                'attribute' => 'bonuscardsCount',
                'filter' =>  $bonuscardsCountFilterHtml,
            ],
            [
                'class' => ActionColumn::className(),
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ],
    ]); ?>
</div>
