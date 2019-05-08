<?php

use kartik\field\FieldRange;

$discountFilterHtml = FieldRange::widget([
    'model' => $searchModel,
    'attribute1' => 'discountFrom',
    'attribute2' => 'discountTo',
    'separator' => '-',
    'template' => '{widget}',
]);    

$bonusquanFilterHtml = FieldRange::widget([
    'model' => $searchModel,
    'attribute1' => 'bonusquanFrom',
    'attribute2' => 'bonusquanTo',
    'separator' => '-',
    'template' => '{widget}',
]);

$minmoneyFilterHtml = FieldRange::widget([
    'model' => $searchModel,
    'attribute1' => 'minmoneyFrom',
    'attribute2' => 'minmoneyTo',
    'separator' => '-',
    'template' => '{widget}',
]);  

$bonuscardsCountFilterHtml = FieldRange::widget([
    'model' => $searchModel,
    'attribute1' => 'bonuscardsCountFrom',
    'attribute2' => 'bonuscardsCountTo',
    'separator' => '-',
    'template' => '{widget}',
]);  