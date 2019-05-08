<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Выберите город';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="orders-station-citySelect">
    <?php foreach ($citiesList as $cityId => $cityName) : ?>
        <p>
            <?= Html::a($cityName, ['index', 'cityId' => $cityId], ['class' => 'text-primary']) ?>
        </p>
    <?php endforeach; ?>
</div>
