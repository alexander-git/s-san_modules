<?php

use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\helpers\Url;

require_once __DIR__.'/_ids.php';

return [
    'class' => ActionColumn::className(),
    'template' => '{view} {update} {delete}',
    'buttons' => [
        'view' => function($url, $model, $key) use ($modalId, $modalHeaderId, $modalContentId) {
            $icon = Html::tag('span', '', [
                'class' => 'showModalButton glyphicon glyphicon-eye-open',
                'style' => 'cursor : pointer',
                'data-modal' => '#'.$modalId,
                'data-modal-header' => '#'.$modalHeaderId,
                'data-modal-content' => '#'.$modalContentId,
                'data-url' => Url::to(['view', 'id' => $model->id]),
                'data-title' => 'Просмотр ('.$model->name.')',
            ]);
            return Html::a($icon, false, [
                'title' => 'Просмотр'
            ]);
        },
        'update' => function($url, $model, $key) use($modalId, $modalHeaderId, $modalContentId) {
            $icon = Html::tag('span', '', [
                'class' => 'showModalButton glyphicon glyphicon-pencil',
                'style' => 'cursor : pointer',
                'data-modal' => '#'.$modalId,
                'data-modal-header' => '#'.$modalHeaderId,
                'data-modal-content' => '#'.$modalContentId,
                'data-url' => Url::to(['update', 'id' => $model->id]),
                'data-title' => 'Обновить ('.$model->name.')',
            ]);
            return Html::a($icon, false, [
                'title' => 'Редактировать'
            ]);
        },
                
        /*
        'delete' => function($url, $model, $key) {   
            $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']);
            return Html::a($icon, $url, [
                'title' => 'Удалить',
                'class' => 'deleteItem',
            ]);
        },      
        */             
    ],
];