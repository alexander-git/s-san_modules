<?php

use kartik\date\DatePicker;

$dateCreateFilterHtml = DatePicker::widget([
    'model' => $searchModel,
    'attribute' => 'dateCreateFrom',
    'attribute2' => 'dateCreateTo',
    'type' => DatePicker::TYPE_RANGE,
    'separator' => '-',
    'pluginOptions' => ['format' => 'dd-mm-yyyy']
]);

$datePublicFilterHtml = DatePicker::widget([
    'model' => $searchModel,
    'attribute' => 'datePublicFrom',
    'attribute2' => 'datePublicTo',
    'type' => DatePicker::TYPE_RANGE,
    'separator' => '-',
    'pluginOptions' => ['format' => 'dd-mm-yyyy']
]);    
