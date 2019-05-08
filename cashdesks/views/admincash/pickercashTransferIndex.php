<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Url;
use app\modules\cashdesks\models\PickercashTransact;
use app\modules\cashdesks\assets\ActionColumnAsset;

/* @var $this yii\web\View */

ActionColumnAsset::register($this);

$this->title = 'Переводы из кассы комплектовщика';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Сейф администратора',
];
$this->params['breadcrumbs'][] = $this->title;


require __DIR__.'/../common/_filtersHtmlPickercash.php';

?>
<div class="cashdesks-admincash-pickercashTransferIndex">
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
                
        'tableOptions' => ['class' => 'table table-bordered'],
        
        'rowOptions' => function ($model, $key, $index, $grid) {
            if ($model->isAccepted) {
                return ['class' => 'bg-success'];
            } else if ($model->isRejected) {
                return ['class' => 'bg-danger'];       
            } else if (!$model->isCreated) {
                return ['class' => 'bg-default'];
            }
        },
                
        'columns' => [
            ['class' => SerialColumn::className()],
            [
                'attribute' => 'date_create',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
                'filter' => $dateCreateFilterHtml,
            ],
            [
                'attribute' => 'date_end',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
                'filter' => $dateEndFilterHtml,
            ],
            [
                'attribute' => 'picker_id',
                'value' => 'pickerName',
                'filter' => $usersList,
            ],
            [
                'attribute' => 'user_id',
                'value' => 'userName',
                'filter' => $usersList,
            ],
            [
                'attribute' => 'state',
                'value' => 'stateName',
                'filter' => PickercashTransact::getStatesArray(),
            ], 
            'banknotes.sum',
            
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {accept} {reject}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']);
                        return Html::a($icon, $url, [
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Просмотр'
                        ]);
                    },         
                    'accept' => function($url, $model, $key) {
                        if ($model->isTypeTransferToAdmincash) {
                            if ($model->isAccepted || $model->isRejected) {
                                return '';
                            }
                        }
                        
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-ok']);
                        return Html::a($icon, $url, [
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Принять'
                        ]);
                    },
                    'reject' => function($url, $model, $key) {
                        if ($model->isTypeTransferToAdmincash) {
                            if ($model->isAccepted || $model->isRejected) {
                                return '';
                            }
                        }
                        
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove']);
                        return Html::a($icon, $url, [
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Отклонить'
                        ]);
                    },   
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    $url = null;
                    
                    if ($action === 'view') {
                        $url = Url::to(['pickercash-transfer-view', 'id' => $model->id]);   
                    } elseif ($action === 'accept') {
                        $url = Url::to(['pickercash-transfer-accept', 'id' => $model->id]);   
                    } elseif ($action === 'reject') {
                        $url = Url::to(['pickercash-transfer-reject', 'id' => $model->id]);   
                    } 
            
                    return $url;
                },
                        
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ],
    ]); ?>

</div>