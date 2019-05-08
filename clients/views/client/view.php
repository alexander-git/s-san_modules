<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use app\modules\clients\assets\ActionColumnAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\clients\models\Client */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

require_once __DIR__.'/../common/_ordersCountFilterHtml.php';

ActionColumnAsset::register($this);
?>
<div class="clients-client-view">
    
    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?php if ($model->bonuscard === null) : ?>
            <?= Html::a('Добавить бонусную карту', ['bonuscard-create', 'clientId' => $model->id], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'fullname',
            'birthday',
            'login',
            'email:email',
            'phone',
            'alterPhone',
            'description:ntext',
            'note:ntext',
            [
                'label' => 'Бонусная карта',
                'attribute' => 'bonuscard.bonuscardType.name',
            ],
            'ordersCount',
            [
                'attribute' => 'state',
                'value' => $model->stateName,
            ],
        ],
    ]) ?>
    
    <h3>Адреса</h3>
    
    <p>
        <?= Html::a('Добавить', ['address-create', 'clientId' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],
            [
                'attribute' => 'cityId',
                'value' => 'cityName',
                'filter' => $citiesList,
            ],
            'street', 
            'home', 
            'appart', 
            'code', 
            'entrance', 
            'floor',
            'name', 
            [
                'attribute' => 'ordersCount',
                'filter' => $ordersCountFilterHtml,
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {update}  {unbind} {delete}',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']);
                        return Html::a($icon, $url, [
                            'title' => 'Просмотр',
                        ]);
                    },         
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
                    'unbind' => function($url, $model, $key) {
                        $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-minus']);
                        return Html::a($icon, $url, [
                            'data-method' => 'post',
                            'data-confirm' => 'Вы уверены?',
                            'title' => 'Отвязать'
                        ]);        
                    }, 
                ],
                'urlCreator' => function ($action, $addressModel, $key, $index) use ($model) {
                    $url = null;
                    $addressId = $addressModel->id;
                    $clientId = $model->id;
                    if ($action === 'view') { 
                        $url = Url::to(['address-view', 'addressId' => $addressId, 'clientId' => $clientId]);   
                    } elseif ($action === 'update') {
                        $url = Url::to(['address-update', 'addressId' => $addressId, 'clientId' => $clientId]);   
                    } elseif ($action === 'delete') {
                        $url = Url::to(['address-delete', 'addressId' => $addressId, 'clientId' => $clientId]);
                    } elseif ($action === 'unbind') {
                        $url = Url::to(['address-unbind', 'addressId' => $addressId, 'clientId' => $clientId]);
                    }
            
                    return $url;
                },      
                'contentOptions' => ['class' => 'actionColumn'],
            ],
        ], 
    ]); ?>

    <?php if ($model->bonuscard !== null) : ?>
        <h3>Бонусная карта</h3>
        
        <p>
            <?= Html::a('Обновить', ['bonuscard-update', 'clientId' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['bonuscard-delete', 'clientId' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p> 
        
        <?= DetailView::widget([
            'model' => $model->bonuscard,
            'attributes' => [
                [
                    'attribute' => 'type',
                    'value' => $model->bonuscard->bonuscardType->name,
                ],
                'moneyquan',
                'bonuses',
            ],
    ]) ?>
    <?php endif; ?>
    
</div>
