<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Url;
use app\modules\cashdesks\models\AdmincashTransact;
use app\modules\cashdesks\assets\ActionColumnAsset;

/* @var $this yii\web\View */

ActionColumnAsset::register($this);

$this->title = 'Переводы от администратора';
$this->params['breadcrumbs'][] = $this->title;

require __DIR__.'/../common/_filtersHtmlAdmincash.php';  

?>
<div class="cashdesks-buhgalter-index">
    
    <?php if ($createdTransfersFromAdmincashCount > 0) : ?>
        <p class="alert alert-danger">
           Есть необработнные переводы от администраторов (<?= $createdTransfersFromAdmincashCount ?>)
        </p>
    <?php endif; ?>
    
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
                'attribute' => 'depart_id',
                'value' => 'departmentName',
                'filter' => $departmentsList,
            ],
            [
                'attribute' => 'date_create',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
                'filter' => $dateCreateFilterHtml,
            ],
            /*
            [
                'attribute' => 'date_end',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
                'filter' => $dateEndFilterHtml,
            ],
            [
                'attribute' => 'date_edit',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
                'filter' => $dateEditFilterHtml,
            ], 
            */
            [
                'attribute' => 'administrator_id',
                'value' => 'administratorName',
                'filter' => $administratorsList,
            ],
            [
                'attribute' => 'user_edit_id',
                'value' => 'userEditName',
                'filter' => $usersEditList,
                
            ],
            'type_value',
            [
                'attribute' => 'state',
                'value' => 'stateName',
                'filter' => AdmincashTransact::getStatesArray(),
            ], 
            'banknotes.sum',
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {update} {delete} {process}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']);
                        return Html::a($icon, $url, [
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Просмотр'
                        ]);
                    },         
                    'update' => function($url, $model, $key) {
                        if ($model->isCreated) {
                            return '';
                        }
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil']);
                        return Html::a($icon, $url, [
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Редактировать'
                        ]);
                    },
                    'delete' => function($url, $model, $key) {
                        if ($model->isCreated) {
                            return '';
                        }
                        
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-ban-circle']);
                        return Html::a($icon, $url, [
                            'data-method' => 'post',
                            'data-confirm' => 'Вы действительно хотите отменить операцию',
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Отменить'
                        ]);
                    },   
                    'process' => function($url, $model, $key) {
                        if ($model->isAccepted || $model->isRejected) {
                            return '';
                        }
                        
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-arrow-right']);
                        return Html::a($icon, $url, [
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Обработать'
                        ]);
                    },
   
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    $url = null;
                    
                    if ($action === 'view') {
                        $url = Url::to(['transfer-view', 'id' => $model->id]);   
                    } elseif ($action === 'update') {
                        $url = Url::to(['transfer-update', 'id' => $model->id]);   
                    } elseif ($action === 'delete') {
                        $url = Url::to(['transfer-delete', 'id' => $model->id]);
                    } elseif ($action === 'process') {
                        $url = Url::to(['transfer-process', 'id' => $model->id]);
                    }
            
                    return $url;
                },
                        
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ],
    ]); ?>

</div>