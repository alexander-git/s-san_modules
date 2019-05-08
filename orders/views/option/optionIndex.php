<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use app\modules\orders\assets\ActionColumnAsset;

$this->title = 'Список';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['settings/index']];
$this->params['breadcrumbs'][] = ['label' => 'Основные настройки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

ActionColumnAsset::register($this);
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
            'name',
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
                },
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ],
    ]); ?>

</div>
