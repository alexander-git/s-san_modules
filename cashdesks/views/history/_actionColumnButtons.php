<?php

use yii\helpers\Html;

return [
    'view' => function($url, $model, $key) {
        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']);
        return Html::a($icon, $url, [
            'class' => 'btn btn-primary btn-sm',
            'title' => 'Просмотр'
        ]);
    },         
    'update' => function($url, $model, $key) {
        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil']);
        return Html::a($icon, $url, [
            'class' => 'btn btn-primary btn-sm',
            'title' => 'Редактировать'
        ]);
    },
    'delete' => function($url, $model, $key) {
        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-ban-circle']);
        return Html::a($icon, $url, [
            'data-method' => 'post',
            'data-confirm' => 'Вы действительно хотите отменить операцию?',
            'class' => 'btn btn-primary btn-sm',
            'title' => 'Отменить'
        ]);
    },   
];