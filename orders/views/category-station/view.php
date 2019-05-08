<?php

use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = $cityName;
$this->params['breadcrumbs'][] = ['label' => 'Станции категорий', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$isCreated = count($models) > 0; 

if ($isCreated) {
    $buttonText = 'Обновить';
    $buttonCssClass = 'btn btn-primary';
} else {
    $buttonText = 'Создать';
    $buttonCssClass = 'btn btn-success';   
}
?>
<div class="orders-categoryStation-view">
    
    <p>
        <?= Html::a($buttonText, ['update' , 'cityId' => $cityId], ['class' => $buttonCssClass]) ?>
   
        <?php if ($isCreated) : ?>
            <?= Html::a('Удалить', ['delete' , 'cityId' => $cityId], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>        
        <?php endif; ?>
    </p>
    
    <?php if ($isCreated) : ?> 
        <table class="table table-bordered  table-striped">
            <thead>
                <tr>
                    <td>
                        Категория
                    </td>
                    <td>
                        Станция
                    </td>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($models as $model) : ?>
                <tr>
                    <td>
                        <?= $categoriesList[$model->category_id] ?>
                    </td>
                    <td>
                        <?= $stationsList[$model->station_id] ?>
                    </td>
                </tr>

            <?php endforeach; ?>
        </tbody>
        </table>
    <?php else: ?>
        <p class="bg bg-danger" style="padding : 15px;">
            Соответсвие станций категориям ешё не задано.
        </p>
    <?php endif; ?>
    
</div>
