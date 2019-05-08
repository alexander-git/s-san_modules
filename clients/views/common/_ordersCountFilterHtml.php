<?php

use kartik\field\FieldRange;

$ordersCountFilterHtml = FieldRange::widget([
    'model' => $searchModel,
    'attribute1' => 'ordersCountFrom',
    'attribute2' => 'ordersCountTo',
    'separator' => '-',
    'template' => '{widget}',
]);    