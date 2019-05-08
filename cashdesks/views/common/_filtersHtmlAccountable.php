<?php

use kartik\date\DatePicker;
use kartik\field\FieldRange;

$dateCreateFilterHtml = DatePicker::widget([
    'model' => $searchModel,
    'attribute' => 'dateCreateFrom',
    'attribute2' => 'dateCreateTo',
    'type' => DatePicker::TYPE_RANGE,
    'separator' => '-',
    'pluginOptions' => ['format' => 'dd-mm-yyyy']
]);

$sumFilterHtml = FieldRange::widget([
    'model' => $searchModel,
    'attribute1' => 'sumFrom',
    'attribute2' => 'sumTo',
    'separator' => '-',
    'template' => '{widget}',
    //'options1' => ['type' => 'number'],
    //'options2' => ['type' => 'number'],
]);    
