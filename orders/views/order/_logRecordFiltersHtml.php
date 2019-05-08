<?php


use kartik\date\DatePicker;


$dateFilterHtml = DatePicker::widget([
    'model' => $searchModel,
    'attribute' => 'dateFrom',
    'attribute2' => 'dateTo',
    'type' => DatePicker::TYPE_RANGE,
    'separator' => '-',
    'pluginOptions' => ['format' => 'dd-mm-yyyy']
]);
