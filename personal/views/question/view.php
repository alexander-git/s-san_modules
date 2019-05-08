<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\grid\SerialColumn;

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Question */

$this->title = $model->name;
$this->params['breadcrumbs'][] = [
    'label' => 'Анкеты', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="personal-question-view">

       <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        
        <?php if ($model->isCreated || $model->isReserve || $model->isCallback) : ?>
            <?= Html::a('Перезвонить', ['callback', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        
        <?php if ($model->isCreated || $model->isCallback || $model->isInterview) : ?>
            <?= Html::a('Занести в резерв', ['reserve', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        
        <?php if ($model->isCreated || $model->isCallback || $model->isReserve || $model->isInterview ) : ?>
            <?= Html::a('Пригласить на собеседование', ['interview', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        
        <?php if ($model->isInterview && !$model->med_book) : ?>
            <?= Html::a('Нужно сделать мед. книжку',  ['make-medbook', 'id' => $model->id], ['data-method' => 'post', 'data-confirm' => 'Вы уверены?', 'class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        
        <?php if ($model->isMakeMedbook && !$model->med_book) : ?>
             <?= Html::a('Мед. книжка готова',  ['make-medbook-complete', 'id' => $model->id], ['data-method' => 'post', 'data-confirm' => 'Вы уверены?', 'class' => 'btn btn-primary']) ?>
        <?php endif; ?>
                   
        <?php if ($model->isCreated || $model->isCallback || $model->isReserve ||$model->isInterview) : ?>
            <?= Html::a('Отказать', ['reject', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        
        <?php if (($model->isInterview) || ($model->isMakeMedbook && $model->med_book)) : ?>
            <?= Html::a('Принять',  ['accept', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        
        <?php 
        /*
        <?php if (!$model->isCreated) : ?>
             <?= Html::a('Вернуть в начальное состояние',  ['return-to-create-state', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?> 
        */
        ?>
    </p>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'post_id',
                'value' => $model->postName,
            ],
            'birthday',
            'city',
            'address',
            [
                'attribute' => 'work_time',
                'value' => $model->workTimeName,
                'format' => 'ntext',
            ],
            'med_book:boolean',
            'children:boolean',
            'smoking:boolean',
            [
                'attribute' => 'about_us_id',
                'value' => $model->aboutUsValueName,
            ],
            'experience:ntext',
            'hobby:ntext',
            'date',
            [
                'attribute' => 'state',
                'value' => $model->stateName,
            ],
        ],
    ]) ?>
    
    <br />
    <h3>История изменений</h3>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'columns' => [
            ['class' => SerialColumn::className()],
            [
                'attribute' => 'date_change',
                'format' => ['date', 'php:d-m-Y'],
            ],
            [
                'attribute' => 'state',
                'value' => 'stateName',
            ],
            'text',
        ],
    ]); ?>

  
</div>
