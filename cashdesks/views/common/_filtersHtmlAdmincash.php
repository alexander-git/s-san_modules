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

$dateEndFilterHtml = DatePicker::widget([
    'model' => $searchModel,
    'attribute' => 'dateEndFrom',
    'attribute2' => 'dateEndTo',
    'type' => DatePicker::TYPE_RANGE,
    'separator' => '-',
    'pluginOptions' => ['format' => 'dd-mm-yyyy']
]);    

$dateEditFilterHtml = DatePicker::widget([
    'model' => $searchModel,
    'attribute' => 'dateEditFrom',
    'attribute2' => 'dateEditTo',
    'type' => DatePicker::TYPE_RANGE,
    'separator' => '-',
    'pluginOptions' => ['format' => 'dd-mm-yyyy']
]);   