<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Выберите город';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<?php foreach ($citiesList as $cityId => $cityName) : ?>
    <p>
        <?= Html::a($cityName, ['menu', 'orderId' => $orderModel->id, 'cityId' => $cityId], ['class' => 'text-primary cityName']) ?>
    </p>
<?php endforeach; ?>

