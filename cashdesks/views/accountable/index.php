<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use app\modules\cashdesks\models\CashdesksApi;
//use app\modules\cashdesks\models\AccountableTransact;

/* @var $this yii\web\View */

$this->title = 'Под отчёт';
$this->params['breadcrumbs'][] = $this->title;

require __DIR__.'/../common/_filtersHtmlAccountable.php';

?>
<div class="cashdesks-accountable-index">    
    <p>
        <?= Html::a('Выдача денег курьеру', ['acctab-courier-issue'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Возврат денег от курьера', ['acctab-courier-return'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Взять для самовывоза', ['acctab-pickup-issue'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Возврат самовывоза', ['acctab-pickup-return'], ['class' => 'btn btn-primary']) ?> 
        <?= Html::a('История операций', ['history'], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <br />
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'depart_id',
                'value' => $model->departmentName,
            ],
            'current',
            'max_sum',
        ],
    ]) ?>
       
    
    <br/>
    
    <?php if ($dataProvider->getTotalCount() > 0) : ?> 
        <h3>Взято под отчёт</h3>
    
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => null,

            'columns' => [
                ['class' => SerialColumn::className()],             
                [
                    'attribute' => 'user_id',
                    'label' => 'Пользователь',
                    'value' => function ($model, $key, $index, $column) 
                    {
                        $value = $model['user_id'];
                        if ((int) $value === 0) {
                            return 'Самовывоз';
                        }
                        if (($value !== null)) {
                            return CashdesksApi::getUserName((int) $value);
                        }
                        return null;
                    },
                ],
                            
                /*
                [
                    'attribute' => 'type',
                    'label' => 'Тип',
                    'value' => function ($model, $key, $index, $column) 
                    {
                        $value = (int) $model['type'];
                        return AccountableTransact::getTypesArray()[$value];
                    },
                ], 
                */
                            
                [
                    'attribute' => 'debt',
                    'label' => 'Долг',
                ], 
                
                
            ],
        ]); ?>
    
    <?php endif; ?>
    
    
</div>
