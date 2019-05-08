<?php

use yii\widgets\DetailView;

?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => 'depart_id',
            'value' => $model->departmentName,
        ],
        [
            'attribute' => 'type',
            'value' => $model->typeName,
        ],
        [
            'attribute' => 'picker_id',
            'value' => $model->pickerName
        ],
        [
            'attribute' => 'user_id',
            'value' => $model->userName,
        ],
        [
            'attribute' => 'date_create',
            'format' => ['datetime', 'php:d-m-Y H:i:s'],
        ],
        'sum',
        'desc',

    ],
]) ?>