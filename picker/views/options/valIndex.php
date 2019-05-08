<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;

$this->title = 'Настройки ('.$departmentName.')';
$this->params['breadcrumbs'][] = [
    'label' => 'Настройки', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="picker-options-optionIndex">
    
    <p>
        <?= Html::a('Создать', ['val-create', 'departmentId' => $departmentId], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],
            [
                'attribute' => 'optionLabel',
                'value' => 'option.label',
            ],
            'val',
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'update') {
                        $url = Url::to(['val-update', 'optId' => $model->opt_id, 'departmentId' => $model->depart_id]);   
                    } elseif ($action === 'delete') {
                        $url = Url::to(['val-delete', 'optId' => $model->opt_id, 'departmentId' => $model->depart_id]);
                    }
                    return $url;
                }

            ],
        ],
    ]); ?>

</div>
