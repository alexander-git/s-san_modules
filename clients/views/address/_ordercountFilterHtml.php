<?php

use kartik\field\FieldRange;

$ordercountFilterHtml = FieldRange::widget([
    'model' => $searchModel,
    'attribute1' => 'ordercountFrom',
    'attribute2' => 'ordercountTo',
    'separator' => '-',
    'template' => '{widget}',
]);    