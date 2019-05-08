<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use app\modules\orders\assets\ActionColumnAsset;

/* @var $this yii\web\View */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;

ActionColumnAsset::register($this);

require_once __DIR__.'/_orderFiltersHtml.php';
?>
<div class="orders-order-index">
    <div class="row">
            <div class="col-md-4">
                <?php $form = ActiveForm::begin([
                    'fieldConfig' => [
                        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    ],
                    'options' => [
                    ],
                    'enableAjaxValidation' => true,
                    'validationUrl' => ['new-order-form-validate'],
                ]); ?>

                <?= $form->field($newOrderFormModel, 'phone',[
                    'options' => [
   
                        'style' => 'width : 100%; margin : 0px; padding : 0px;',
                    ],
                    'labelOptions' => [
                        'style' => 'width : auto; padding-right : 10px;',
                        'class' => 'pull-left text-left',
                    ],
                    'wrapperOptions' => [
                        'style' => 'overflow : hidden;',
                    ],
                ])->textInput([
                    'maxlength' => true,
                ]) ?>
            </div>
            <div class="col-md-2">
                <?= Html::submitButton('Новый заказ', ['class' => ' btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?> 
    </div>
    
    <br />
    
    <p>
        <?= Html::a('Очистить фильтры', ['index'], ['class' => 'btn btn-primary']) ?> 
    </p>
    <br /> 
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        
        'tableOptions' => ['class' => 'table table-bordered'],
        'rowOptions' => function ($model, $key, $index, $grid) {
            if ($model->isExpired()) {
                return ['class' => 'bg-danger'];
            } else {
                return ['class' => 'bg-default'];       
            }
        },
        
        'columns' => [
            'id',
            [
                'attribute' => 'city_id',
                'value' => function ($model, $key, $index, $column) use ($citiesList) {
                    return $citiesList[$model->city_id];
                },
                'filter' => $citiesList,
            ],
            'order_num',
            [
                'attribute' => 'stage_id',
                'value' => function ($model, $key, $index, $column) use ($stagesList) {
                    return $stagesList[$model->stage_id];
                },
                'filter' => $stagesList,   
            ],
            [
                'attribute' => 'total_price',
                'filter' => $totalPriceFilterHtml,
            ],
            [
                'attribute' => 'start_date',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
                'filter' => $startDateFilterHtml,
            ],
            [
                'attribute' => 'delivery_date',
                'filter' => $deliveryDateFilterHtml,
            ],
            [
                'attribute' => 'delivery_time',
                'filter' => $deliveryTimeFilterHtml,
            ],
            'phone',
            'address',
            [
                'attribute' => 'payment_type',
                'value' => function ($model, $key, $index, $column) use ($paymentTypesList) {
                    return $paymentTypesList[$model->payment_type];
                },
                'filter' => $paymentTypesList,
            ],
            [
                'class' => ActionColumn::className(),
                'contentOptions' => ['class' => 'actionColumn'],
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) use ($newStageId) {
                    $url = null;
                    $isNew = $model->stage_id === $newStageId;
                    if ($action === 'view') {
                         if ($isNew) {
                            $url = Url::to(['cart', 'orderId' => $model->id]);
                        } else {
                            $url = Url::to(['summary', 'orderId' => $model->id]); 
                        }
                    } 
   
                    return $url;
                },
            ],
        ],
    ]); ?>
    
</div>
