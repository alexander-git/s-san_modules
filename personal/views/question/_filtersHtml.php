<?php

use kartik\date\DatePicker;

$birthdayFilterHtml = DatePicker::widget([
    'model' => $searchModel,
    'attribute' => 'birthdayFrom',
    'attribute2' => 'birthdayTo',
    'type' => DatePicker::TYPE_RANGE,
    'separator' => '-',
    'pluginOptions' => ['format' => 'dd-mm-yyyy']
]);