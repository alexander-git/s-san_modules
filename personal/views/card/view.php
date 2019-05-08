<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use yii\helpers\Url;
use app\modules\personal\assets\ActionColumnAsset;
use app\modules\personal\models\DocsList;


/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Card */

$this->title = $model->name;
$this->params['breadcrumbs'][] = [
    'label' => 'Карточки сотрудников', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

ActionColumnAsset::register($this);
?>
<div class="personal-card-view">
    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Обновить документы', ['update-docs-list', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        
        <?php if ($model->medbook !== null) : ?>
            <?= Html::a('Редактировать мед. книжку', ['update-medbook', 'cardId' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'post_id',
                'value' => $model->postName,
            ],
            [
                'attribute' => 'depart_id',
                'value' => $model->departmentName,
            ],
            'birthday',
            'rate',
            'phone',
            'address',
            'med_book:boolean',
            'student:boolean',
            'docs_ok:boolean',
            'date_employment',
            'date_obt_input',
            'date_obt_first',
            [
                'attribute' => 'state',
                'value' => $model->stateName,
            ],
        ],
    ]) ?>
    
    <h3> Документы </h3>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],
            'name',
            [
                'attribute' => 'type',
                'value' => 'typeName',
                'filter' => DocsList::getTypesArray(),
            ],
            [
                'attribute' => 'check',
                'label' => 'Наличие',
                'format' => 'boolean',
                'value' => function($model, $key, $index, $column) {
                    if (count($model->cardDocs) === 0) {
                        return false; 
                    }
                    return $model->cardDocs[0]->check;
                },
                'filter' => $yesNoList,
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{create} {view} {update} {delete}',
                'buttons' => [
                     'create' => function($url, $model, $key) {
                        if (count($model->cardDocs) !== 0) {
                            return '';
                        }
                    
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus']);
                        return Html::a($icon, $url, [
                            'title' => 'Добавить'
                        ]);
                    },  
                    'view' => function($url, $model, $key) {
                        if (count($model->cardDocs) === 0) {
                            return '';
                        }
                        if (!$model->isTypeLoadable) {
                            return '';
                        }
                    
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']);
                        return Html::a($icon, $url, [
                            'title' => 'Просмотр',
                        ]);
                    },         
                    'update' => function($url, $model, $key) {
                        if (count($model->cardDocs) === 0) {
                            return '';
                        }
                        if (!$model->isTypeLoadable) {
                            return '';
                        }
                        
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil']);
                        return Html::a($icon, $url, [
                            'title' => 'Редактировать'
                        ]);
                    },
                    'delete' => function($url, $model, $key) {
                        if (count($model->cardDocs) === 0) {
                            return '';
                        }
                        
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']);
                        return Html::a($icon, $url, [
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'title' => 'Удалить'
                        ]);
                    },   
                ],
                'urlCreator' => function ($action, $docsListModel, $key, $index) use ($model) {
                    $url = null;
                    $docsId = $docsListModel->id;
                    $cardId = $model->id;
                    if ($action === 'create') {
                        $url = Url::to(['card-docs-create', 'cardId' => $cardId, 'docsId' => $docsId]); 
                    } elseif ($action === 'view') { 
                        $url = Url::to(['card-docs-view', 'cardId' => $cardId, 'docsId' => $docsId]);   
                    } elseif ($action === 'update') {
                        $url = Url::to(['card-docs-update', 'cardId' => $cardId, 'docsId' => $docsId]);   
                    } elseif ($action === 'delete') {
                        $url = Url::to(['card-docs-delete', 'cardId' => $cardId, 'docsId' => $docsId]);
                    }
            
                    return $url;
                },        
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ], 
    ]); ?>
    
    <?php if ($model->medbook !== null) : ?>
    
        <h3>Медицинаская книжка</h3>
    
        <?= DetailView::widget([
            'model' => $model->medbook,
            'attributes' => [
                'date_sanmin',
                'date_sanmin_end',
                'date_exam',
                'date_exam_end',
            ],
        ]) ?>

    <?php endif; ?>
    
</div>
