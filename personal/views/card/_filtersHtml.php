<?php

use kartik\date\DatePicker;
use kartik\field\FieldRange;

$birthdayFilterHtml = DatePicker::widget([
    'model' => $searchModel,
    'attribute' => 'birthdayFrom',
    'attribute2' => 'birthdayTo',
    'type' => DatePicker::TYPE_RANGE,
    'separator' => '-',
    'pluginOptions' => ['format' => 'dd-mm-yyyy']
]);

$dateEmploymentFilterHtml = DatePicker::widget([
    'model' => $searchModel,
    'attribute' => 'dateEmploymentFrom',
    'attribute2' => 'dateEmploymentTo',
    'type' => DatePicker::TYPE_RANGE,
    'separator' => '-',
    'pluginOptions' => ['format' => 'dd-mm-yyyy']
]);

$dateObtInputFilterHtml = DatePicker::widget([
    'model' => $searchModel,
    'attribute' => 'dateObtInputFrom',
    'attribute2' => 'dateObtInputTo',
    'type' => DatePicker::TYPE_RANGE,
    'separator' => '-',
    'pluginOptions' => ['format' => 'dd-mm-yyyy']
]);    

$dateObtFirstFilterHtml = DatePicker::widget([
    'model' => $searchModel,
    'attribute' => 'dateObtFirstFrom',
    'attribute2' => 'dateObtFirstTo',
    'type' => DatePicker::TYPE_RANGE,
    'separator' => '-',
    'pluginOptions' => ['format' => 'dd-mm-yyyy']
]);

$rateFilterHtml = FieldRange::widget([
    'model' => $searchModel,
    'attribute1' => 'rateFrom',
    'attribute2' => 'rateTo',
    'separator' => '-',
    'template' => '{widget}',
]);    
