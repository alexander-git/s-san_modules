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
            'attribute' => 'state',
            'value' => $model->stateName,
        ],
        [
            'attribute' => 'administrator_id',
            'value' => $model->administratorName
        ],
        'type_value',
        [
            'attribute' => 'user_id',
            'value' => $model->userName,
        ],
        [
            'attribute' => 'date_create',
            'format' => ['datetime', 'php:d-m-Y H:i:s'],
        ],
        [
            'attribute' => 'date_end',
            'format' => ['datetime', 'php:d-m-Y H:i:s'],
        ],

        [
            'attribute' => 'user_edit_id',
            'value' => $model->userEditName,
        ],

        [
            'attribute' => 'date_edit',
            'format' => ['datetime', 'php:d-m-Y H:i:s'],
        ],

        'desc',

        'banknotes.count_5000',
        'banknotes.count_1000',
        'banknotes.count_500',
        'banknotes.count_100',
        'banknotes.count_50',
        'banknotes.rest',
        'banknotes.sum',
    ],
]) ?>