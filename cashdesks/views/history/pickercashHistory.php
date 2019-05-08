<?php

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Url;
use app\modules\cashdesks\models\PickercashTransact;
use app\modules\cashdesks\assets\ActionColumnAsset;

/* @var $this yii\web\View */

ActionColumnAsset::register($this);

$this->title = 'Касса комплектовщика';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'История',
];
$this->params['breadcrumbs'][] = $this->title;


require __DIR__.'/../common/_filtersHtmlPickercash.php';

$actionColumnButtons = require __DIR__.'/_actionColumnButtons.php';

?>
<div class="cashdesks-pickercash-history">
    
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

            [
                'attribute' => 'date_end',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
                'filter' => $dateEndFilterHtml,
            ],
            [
                'attribute' => 'type',
                'value' => 'typeNameAdvanced',
                'filter' => PickercashTransact::getTypesArray(),
            ],
            [
                'attribute' => 'picker_id',
                'value' => 'pickerName',
                'filter' => $pickersList,
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
                'template' => '{view} {update} {delete}',
                'buttons' => $actionColumnButtons,
                'urlCreator' => function ($action, $model, $key, $index) {
                    $url = null;
                    
                    if ($action === 'view') {
                        $url = Url::to(['pickercash-transact-view', 'id' => $model->id]);   
                    } elseif ($action === 'update') {
                        $url = Url::to(['pickercash-transact-update', 'id' => $model->id]);   
                    } elseif ($action === 'delete') {
                        $url = Url::to(['pickercash-transact-delete', 'id' => $model->id]);
                    }
            
                    return $url;
                },
                        
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ],
    ]); ?>

</div>