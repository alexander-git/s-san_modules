<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Url;
use app\modules\picker\models\search\ShiftsCourierSearch;

/* @var $this yii\web\View */

$this->title = 'Смена';
$this->params['breadcrumbs'][]= $this->title;
?>
<div class="picker-view-pickerIndex">
    <?= $this->render('_shiftPickerState') ?>

    <p>
        <?= Html::a('Закрыть смену', ['shift-picker-close'], ['class' => 'btn btn-primary']) ?>
    </p>
    <br />
    <p>
        <?= Html::a('Открыть смену курьера', ['shift-courier-open-default'], ['class' => 'btn btn-success']) ?>
    </p>

    <h3>Смены курьеров</h3>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered'],
        
        'rowOptions' => function ($model, $key, $index, $grid) {
            if ($model->isClosed) {
                return ['class' => 'bg-success'];
            } else {
                return ['class' => 'bg-default'];       
            }
        },
        
        'columns' => [
            ['class' => SerialColumn::className()],
        
            [
                'attribute' => 'date_open',
                'format' => ['datetime', 'php:d-m H:i:s'],
                'filter' => false,
            ],
            
            [
                'attribute' => 'date_close',
                'format' => ['datetime', 'php:d-m H:i:s'],
                'filter' => false,
            ],    
            
            'courier_name',
            'courier_phone',
            
            [
                'attribute' => 'type_courier',
                'filter' => ShiftsCourierSearch::getTypeCouriersArrayFilter(),
                'value' => 'typeCourierName',
            ],
            [
                'attribute' => 'state',
                'filter' => ShiftsCourierSearch::getStatesArray(),
                'value' => 'stateName',
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{shiftCourierCloseFill} {shiftCourierUpdate} {shiftCourierDelete}',
                'buttons' => [
                     'shiftCourierCloseFill' => function($url, $model, $key) {
                        if ($model->isClosed) {
                            return '';
                        }
                        
                        if ($model->isOpened) {
                            $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-lock']);
                            return Html::a($icon, $url, [
                                'class' => 'btn btn-primary btn-sm',
                                'title' => 'Закрыть смену'
                            ]);
                        }
                    },   
                    'shiftCourierUpdate' => function($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil']);
                        return Html::a($icon, $url, [
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Редактировать'
                        ]);
              
                    },
                    'shiftCourierDelete' => function($url, $model, $key) {
                        if ($model->isClosed) {
                            return '';
                        }
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']);
                        return Html::a($icon, $url, [
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Удалить'
                        ]);
                    },   
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    $url = null;
                    
                    if ($action === 'shiftCourierCloseFill') {
                        $url = Url::to(['shift-courier-close-fill', 'id' => $model->id]);   
                    } elseif ($action === 'shiftCourierUpdate') {
                        if ($model->isOpened) {
                            if ($model->isTypeCourierDefault) {
                                $url = Url::to(['shift-courier-update-default', 'id' => $model->id]);   
                            } elseif ($model->isTypeCourierAdditional) {
                                $url = Url::to(['shift-courier-update-additional', 'id' => $model->id]);
                            }
                        } else {
                            // Если смена закрыта то редактирование повторяет 
                            // процесс закрытия. С той разницей что дата 
                            // закрытия уже не меняется.
                            $url = Url::to(['shift-courier-close-fill', 'id' => $model->id]); 
                        }
                    } elseif ($action === 'shiftCourierDelete') {
                        $url = Url::to(['shift-courier-delete', 'id' => $model->id]);   
                    }
                    
                    return $url;
                }
            ],
        ],
    ]); ?>  
    

</div>