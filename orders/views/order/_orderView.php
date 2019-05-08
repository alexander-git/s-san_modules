<?php

use yii\widgets\DetailView;

?>

<?= DetailView::widget([
    'model' => $orderModel,
    'attributes' => [
        'order_num',
        [
            'attribute' => 'client_id',
            'value' => $orderModel->clientName,
        ],
        'recipient',
        'phone',
        'alter_phone',
        [
            'attribute' => 'user_id',
            'value' => $orderModel->userName,
        ],
        [
            'attribute' => 'start_date',
            'format' => ['datetime', 'php:d-m-Y H:i:s'],
        ],
        [
            'attribute' => 'update_date',
            'format' => ['datetime', 'php:d-m-Y H:i:s'],
        ],
        [
            'attribute' => 'end_date',
            'format' => ['datetime', 'php:d-m-Y H:i:s'],
        ],
        [
            'attribute' => 'city_id',
            'value' => $orderModel->cityName,
        ],
        'address',
        'person_num',
        'items_count',
        'total_price',
        'total_pay',
        'tax',
        'is_paid:boolean',
        [
            'attribute' => 'payment_type',
            'value' => $orderModel->getPaymentTypeName(),
        ],
        'return_sum',
        'is_postponed:boolean',
        'delivery_date',
        'delivery_time',
        [
            'attribute' => 'stage_id',
            'value' => $orderModel->getStageName(),
        ],
        'is_deleted:boolean',
        'comment',
    ],
]) ?>

<?php if ($orderModel->deliveryInfo !== null) : ?>
    <br />
    <h4>Информация о доставке</h4>
    <?= DetailView::widget([
        'model' => $orderModel->deliveryInfo,
        'attributes' => [
            'planned_delivery_date',
            'planned_delivery_time',
            'price',
        ],
    ]) ?>
<?php endif; ?>