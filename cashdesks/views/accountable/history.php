<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Url;
use app\modules\cashdesks\models\search\AccountableTransactPickerSearch;
use app\modules\cashdesks\assets\ActionColumnAsset;

/* @var $this yii\web\View */

ActionColumnAsset::register($this);

$this->title = 'История операций';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Под отчёт',
];
$this->params['breadcrumbs'][] = $this->title;


require __DIR__.'/../common/_filtersHtmlAccountable.php';

?>
<div class="cashdesks-pickercash-history">
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
                                
        'columns' => [
            ['class' => SerialColumn::className()],
            [
                'attribute' => 'date_create',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
                'filter' => $dateCreateFilterHtml,
            ],

            [
                'attribute' => 'user_id',
                'value' => 'userName',
                'filter' => $usersList,
            ],
        
            [
                'attribute' => 'type',
                'value' => 'typeName',
                'filter' => AccountableTransactPickerSearch::getTypesArrayPicker(),
            ],
            [
                'attribute' => 'sum',
                'filter' => $sumFilterHtml,
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']);
                        return Html::a($icon, $url, [
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Просмотр'
                        ]);
                    },         
                    'update' => function($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil']);
                        return Html::a($icon, $url, [
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Редактировать'
                        ]);
                    },
                    'delete' => function($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-ban-circle']);
                        return Html::a($icon, $url, [
                            'data-method' => 'post',
                            'data-confirm' => 'Вы действительно хотите отменить операцию?',
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Отменить'
                        ]);
                    },   
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    $url = null;
                    
                    if ($action === 'view') {
                        $url = Url::to(['transact-view', 'id' => $model->id]);   
                    } elseif ($action === 'update') {
                        $url = Url::to(['transact-update', 'id' => $model->id]);   
                    } elseif ($action === 'delete') {
                        $url = Url::to(['transact-delete', 'id' => $model->id]);
                    }
            
                    return $url;
                },
                        
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ],
    ]); ?>

</div>
