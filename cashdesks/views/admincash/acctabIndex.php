<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Url;
use app\modules\cashdesks\models\AdmincashTransact;

/* @var $this yii\web\View */

$this->title = 'Под отчёт';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Сейф администратора',
];
$this->params['breadcrumbs'][] = $this->title;

require __DIR__.'/../common/_filtersHtmlAdmincash.php';

?>
<div class="cashdesks-admincash-acctabIndex">
    
    <p>
        <?= Html::a('Выдать деньги пользователю', ['acctab-user-create'], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <br />
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
                
        'tableOptions' => ['class' => 'table table-bordered'],
        
        'rowOptions' => function ($model, $key, $index, $grid) {
            if ($model->isAccepted) {
                return ['class' => 'bg-success'];
            } else if ($model->isRejected) {
                return ['class' => 'bg-danger'];       
            } else if (!$model->isCreated) {
                return ['class' => 'bg-default'];
            }
        },
                
        'columns' => [
            ['class' => SerialColumn::className()],
            [
                'attribute' => 'date_create',
                'format' => ['datetime', 'php:d-m H:i:s'],
                'filter' => $dateCreateFilterHtml,
            ],
            [
                'attribute' => 'date_end',
                'format' => ['datetime', 'php:d-m H:i:s'],
                'filter' => $dateEndFilterHtml,
            ],
            [
                'attribute' => 'administrator_id',
                'value' => 'administratorName',
                'filter' => $administratorsList,
            ],
            [
                'attribute' => 'user_id',
                'value' => 'userName',
                'filter' => $usersList,
            ],
            [
                'attribute' => 'state',
                'value' => 'stateName',
                'filter' => AdmincashTransact::getStatesArrayAcctabUser(),
            ],
            'banknotes.sum',
            [
                'class' => ActionColumn::className(),
                'template' => '{return}',
                'buttons' => [
                    'return' => function($url, $model, $key) {
                        if ($model->isAccepted) {
                            return '';
                        }
                        
                        if ($model->isCreated) {
                            $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-arrow-left']);
                            return Html::a($icon, $url, [
                                'class' => 'btn btn-primary btn-sm',
                                'title' => 'Возврат денег'
                            ]);
                        }
                    }, 
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    $url = null;
                    
                    if ($action === 'return')  {
                        $url = Url::to(['acctab-user-return', 'id' => $model->id]);   
                    } 

                    return $url;
                }
            ],
        ],
    ]); ?>

</div>