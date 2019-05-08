<?php

use yii\widgets\MaskedInput;
use kartik\date\DatePicker;
use kartik\field\FieldRange;

$startDateFilterHtml = DatePicker::widget([
    'model' => $searchModel,
    'attribute' => 'startDateFrom',
    'attribute2' => 'startDateTo',
    'type' => DatePicker::TYPE_RANGE,
    'separator' => '-',
    'pluginOptions' => ['format' => 'dd-mm-yyyy']
]);

$deliveryDateFilterHtml = DatePicker::widget([
    'model' => $searchModel,
    'attribute' => 'deliveryDateFrom',
    'attribute2' => 'deliveryDateTo',
    'type' => DatePicker::TYPE_RANGE,
    'separator' => '-',
    'pluginOptions' => ['format' => 'dd-mm-yyyy']
]);

$deliveryTimeFilterHtml = FieldRange::widget([
    'model' => $searchModel,
    'type' => FieldRange::INPUT_WIDGET,
    'widgetClass' => MaskedInput::className(),
    'attribute1' => 'deliveryTimeFrom',
    'attribute2' => 'deliveryTimeTo',
    'widgetOptions1' => [
        'mask' => '99:99',
        'options' => [
            'class' => 'form-control',
        ]
    ],
    'widgetOptions2' => [
        'mask' => '99:99',
        'options' => [
            'class' => 'form-control',
        ]
    ],
    'separator' => '-',
    'template' => '{widget}',
]);    

$totalPriceFilterHtml = FieldRange::widget([
    'model' => $searchModel,
    'attribute1' => 'totalPriceFrom',
    'attribute2' => 'totalPriceTo',
    'separator' => '-',
    'template' => '{widget}',
]);    