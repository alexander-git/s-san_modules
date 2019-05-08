<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use app\modules\clients\assets\ActionColumnAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\clients\models\Address */

$this->title = $model->compositeName;
$this->params['breadcrumbs'][] = ['label' => 'Адреса', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

require_once __DIR__.'/_ordercountFilterHtml.php';

ActionColumnAsset::register($this);
?>
<div class="clients-address-view">

    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            [
                'attribute' => 'cityId',
                'value' => $model->cityName,
            ],
            'street',
            'home',
            'appart',
            'floor',
            'code',
            'entrance',
            'name',
            'desc:ntext',
            'ordersCount',
        ],
    ]) ?>
    
    <h3>Клиенты</h3>
    
    <p>
        <?= Html::a('Добавить клиента', ['client-address-create', 'addressId' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],
            [
                'attribute' => 'fullname',
                'label' => 'Полное имя',
                'value' => 'client.fullname',
            ],
            [
                'attribute' => 'ordercount',
                'filter' => $ordercountFilterHtml,
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil']);
                        return Html::a($icon, $url, [
                            'title' => 'Редактировать'
                        ]);
                    },
                    'delete' => function($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']);
                        return Html::a($icon, $url, [
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'title' => 'Удалить'
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $clientAddressModel, $key, $index) use ($model) {
                    $url = null;
                    $addressId = $clientAddressModel->addressId;
                    $clientId = $clientAddressModel->clientId;
                    if ($action === 'update') {
                        $url = Url::to(['client-address-update', 'addressId' => $addressId, 'clientId' => $clientId]);   
                    } elseif ($action === 'delete') {
                        $url = Url::to(['client-address-delete', 'addressId' => $addressId, 'clientId' => $clientId]);
                    } 
            
                    return $url;
                },      
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ], 
    ]); ?>
    
    

</div>
