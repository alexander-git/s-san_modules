<?php

use yii\widgets\DetailView;

?>

<?=DetailView::widget([
    'model' => $model,
    'attributes' => [
        'name',
        [
            'attribute' => 'type',
            'value' => $model->typeName,
        ],
    ],
])?>