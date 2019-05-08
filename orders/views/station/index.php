<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Станции ('.$cityName.')';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="orders-station-index">
    <p>
        <?= Html::a('Сменить город', ['city-select'], ['class' => 'text-primary']) ?>
    </p>

    <?php foreach ($stationsList as $stationId => $stationName) : ?>
        <p>
            <?php if ($stationId === $pickStationId) : ?>
                <?= Html::a($stationName, ['station-pick', 'cityId' => $cityId], ['class' => 'btn btn-primary']) ?>
            <?php else : ?>
                 <?= Html::a($stationName, ['station', 'stationId' => $stationId, 'cityId' => $cityId], ['class' => 'btn btn-primary']) ?>
            <?php endif; ?> 
        </p>
    <?php endforeach; ?>
</div>
