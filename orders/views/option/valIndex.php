<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;

$this->title = $cityName;
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['settings/index']];
$this->params['breadcrumbs'][] = ['label' => 'Основные настройки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="orders-option-valIndex">
    
    <p>
        <?= Html::a('Создать', ['val-create', 'cityId' => $cityId], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],
            'option_id',
            [
                'attribute' => 'optionName',
                'label' => 'Опция',
                'value' => 'option.name',
            ],
            'value',
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'update') {
                        $url = Url::to(['val-update', 'optionId' => $model->option_id, 'cityId' => $model->city_id]);   
                    } elseif ($action === 'delete') {
                        $url = Url::to(['val-delete', 'optionId' => $model->option_id, 'cityId' => $model->city_id]);
                    }
                    return $url;
                }

            ],
        ],
    ]); ?>

</div>
