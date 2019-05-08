<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Url;
use app\modules\cashdesks\models\ExpenseType;

$this->title = 'Виды расходов';
$this->params['breadcrumbs'][] = [
    'url' => ['service/index'],
    'label' => 'Управление',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="checkdesk-expenseType-index">
    
    <p>
        <?= Html::a('Создать', ['type-create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],
            'name',
            [
                'attribute' => 'type',
                'value' => 'typeName',
                'filter' => ExpenseType::getTypesArray(),
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    $url = null;
                    if ($action === 'update') {
                        $url = Url::to(['type-update', 'id' => $model->id]);   
                    } elseif ($action === 'delete') {
                        $url = Url::to(['type-delete', 'id' => $model->id]);
                    }
                    return $url;
                }
            ],
        ],
    ]); ?>

</div>
