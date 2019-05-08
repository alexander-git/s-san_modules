<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;

$this->title = 'Обновить('.$model->name.')';
$this->params['breadcrumbs'][] = [
    'url' => ['service/index'],
    'label' => 'Управление',
];
$this->params['breadcrumbs'][] = [
    'label' => 'Виды расходов', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-expenseType-typeUpdate">

    <?= $this->render('_typeForm', ['model' => $model]) ?>

    <?php if ($model->isTypeSupplier || $model->isTypeArray) : ?>
    
        <h3>Значения</h3>
        <div>
            <p>
                <?= Html::a('Добавить', ['item-create', 'expenseTypeId' => $model->id], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => SerialColumn::className()],
                    'value',
                    [
                        'class' => ActionColumn::className(),
                        'template' => '{update} {delete}',
                        'urlCreator' => function ($action, $expenseTypeItemModel, $key, $index) {
                            $url = null;

                            if ($action === 'update') {
                                $url = Url::to([
                                    'item-update',
                                    'expenseTypeId' => $expenseTypeItemModel->expense_type_id,
                                    'id' => $expenseTypeItemModel->id
                                ]);   
                            } elseif ($action === 'delete') {
                                $url = Url::to([
                                    'item-delete',
                                    'expenseTypeId' => $expenseTypeItemModel->expense_type_id,
                                    'id' => $expenseTypeItemModel->id
                                ]);
                            }

                            return $url;
                        }
                    ],
                ],
            ]); ?>
        </div>
    
    <?php endif; ?>

</div>
