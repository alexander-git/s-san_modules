<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
/* @var $this yii\web\View */

if ($isUpdate) {
    $title = 'Обновить';
    $buttonText = $title;
    $buttonCssClass = 'btn btn-primary';
} else {
    $title = 'Создать';
    $buttonText = $title;
    $buttonCssClass = 'btn btn-success';
}


$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Станции категорий', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $cityName, 'url' => ['view', 'cityId' => $cityId]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="orders-categoryStation-update">

    <div class="categoryStationsForm">
        
        <?php $form = ActiveForm::begin(); ?>

            <?php foreach ($models as $id => $model) : ?>
                <?php $categoryName = $categoriesList[$model->category_id] ?>

                <?= $form->field($model, "[$id]station_id")
                    ->dropDownList($stationsList)     
                    ->label($categoryName); 
                ?>

            <?php endforeach; ?>

            <div class="form-group">
                <?= Html::submitButton($buttonText, ['class' => $buttonCssClass]) ?>
            </div>
        
        
        <?php ActiveForm::end(); ?>
        
    </div>

</div>
