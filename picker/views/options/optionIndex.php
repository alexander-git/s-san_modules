<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Url;

$this->title = 'Список настроек';
$this->params['breadcrumbs'][] = [
    'label' => 'Настройки', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="picker-options-optionIndex">
    
    <p>
        <?= Html::a('Создать', ['option-create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],
            'id',
            'label',
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'update') {
                        $url = Url::to(['option-update', 'id' => $model->id]);   
                    } elseif ($action === 'delete') {
                        $url = Url::to(['option-delete', 'id' => $model->id]);
                    }
                    return $url;
                }

            ],
        ],
    ]); ?>

</div>
