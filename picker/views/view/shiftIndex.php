<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Url;
use app\modules\picker\helpers\TotalHelper;

/* @var $this yii\web\View */

$this->title = 'Суточная смена';
$this->params['breadcrumbs'][]= $this->title;


$countOrderTotal = TotalHelper::getTotal($dataProvider->models, 'count_order');
$checkSumTotal = TotalHelper::getTotal($dataProvider->models, 'check_sum');
$cashTotal = TotalHelper::getTotal($dataProvider->models, 'cash');
$checkNocashTotal = TotalHelper::getTotal($dataProvider->models, 'check_nocash');
$spendTotal = TotalHelper::getTotal($dataProvider->models, 'spend');
$giftsTotal = TotalHelper::getTotal($dataProvider->models, 'gifts');


?>
<div class="picker-view-shiftIndex">

    <?php if (!$shiftsCourierPickupModel->isClosed) : ?>
        <p class="alert alert-danger">
           Перед закрытием суточной смены нужно
           <?= Html::a('закрыть смену самовывоза', ['shift-courier-pickup-close-fill'], ['class' => 'alert-link']) ?>.
        </p>
    <?php else: ?>
        <?= Html::a('Закрыть суточную смену', ['shift-close-fill'], ['class' => 'btn btn-primary']) ?> 
    <?php endif; ?>
    
    <h3>Все смены курьеров</h3>
   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'showFooter' => true,        
        'footerRowOptions' => [
            'style' => 'font-weight : bold;'
        ],
        'columns' => [          
            'courier_name',  
            [
                'attribute' => 'count_order',
                'footer' => $countOrderTotal
            ],
            [
                'attribute' => 'check_sum',
                'footer' => $checkSumTotal
            ],
            [
                'attribute' => 'cash',
                'footer' => $cashTotal,
            ],
            [
                'attribute' => 'check_nocash',
                'footer' => $checkNocashTotal,
            ],
            [
                'attribute' => 'spend',
                'footer' => $spendTotal,
            ],
            [
                'attribute' => 'gifts',
                'footer' => $giftsTotal,
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{shiftCourierUpdate}',
                'buttons' => [ 
                    'shiftCourierUpdate' => function($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil']);
                        return Html::a($icon, $url, [
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Редактировать'
                        ]);
              
                    },
                ],        
                'urlCreator' => function ($action, $model, $key, $index) {
                    $url = null;
                    
                    if ($action === 'shiftCourierUpdate') {
                        if ($model->isTypeCourierPickup) {
                            $url = Url::to(['shift-courier-pickup-close-fill']); 
                        } else {
                            $url = Url::to(['shift-courier-check-fill', 'id' => $model->id]);
                        }
                    } 
                    
                    return $url;
                },
            ]
        ],
    ]); ?>  
    
</div>