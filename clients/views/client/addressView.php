<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\clients\models\Client */

$this->title = 'Адрес ('.$addressModel->compositeName.')';
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $clientModel->name, 'url' => ['view', 'id' => $clientModel->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="clients-client-addressView">
    
    <p>
        <?= Html::a('Обновить', ['address-update', 'clientId' => $clientModel->id, 'addressId' => $addressModel->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['address-delete', 'clientId' => $clientModel->id, 'addressId' => $addressModel->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    
    <?= DetailView::widget([
        'model' => $addressModel,
        'attributes' => [
            [
                'attribute' => 'cityId',
                'value' => $addressModel->cityName,
            ],
            'street',
            'home',
            'appart',
            'code',
            'entrance',
            'floor',
            'name',
            'desc:ntext',
            [
                'label' => 'Количество заказов',
                'value' => $clientAddressModel->ordercount
            ],
        ],
    ]) ?>
</div>