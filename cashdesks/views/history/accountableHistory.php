<?php

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Url;
use app\modules\cashdesks\models\AccountableTransact;
use app\modules\cashdesks\assets\ActionColumnAsset;

/* @var $this yii\web\View */

ActionColumnAsset::register($this);

$this->title = 'Под отчёт';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'История',
];
$this->params['breadcrumbs'][] = $this->title;



require __DIR__.'/../common/_filtersHtmlAccountable.php';

$actionColumnButtons = require __DIR__.'/_actionColumnButtons.php';

?>
<div class="cashdesks-history-accountableHistory">
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
                                
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
                'attribute' => 'type',
                'value' => 'typeName',
                'filter' => AccountableTransact::getTypesArray(),
            ],
            [
                'attribute' => 'sum',
                'filter' => $sumFilterHtml,
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {update} {delete}',
                'buttons' => $actionColumnButtons,
                'urlCreator' => function ($action, $model, $key, $index) {
                    $url = null;
            
                    if ($action === 'view') {
                        $url = Url::to(['accountable-transact-view', 'id' => $model->id]);   
                    } elseif ($action === 'update') {
                        $url = Url::to(['accountable-transact-update', 'id' => $model->id]);   
                    } elseif ($action === 'delete') {
                        $url = Url::to(['accountable-transact-delete', 'id' => $model->id]);
                    }
            
                    return $url;
                },
                        
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ],
    ]); ?>

</div>
