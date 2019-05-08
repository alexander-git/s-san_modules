<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use app\modules\orders\assets\ActionColumnAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\orders\models\search\StageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Стадии заказов';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['settings/index']];
$this->params['breadcrumbs'][] = $this->title;

ActionColumnAsset::register($this);
?>
<div class="orders-stage-index">

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'sort',
            'name',
            [
                'class' => ActionColumn::className(),
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ],
    ]); ?>
    
</div>
