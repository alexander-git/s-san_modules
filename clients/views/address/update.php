<?php

/* @var $this yii\web\View */
/* @var $model app\modules\clients\models\Address */

$this->title = 'Обновить';
$this->params['breadcrumbs'][] = ['label' => 'Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->compositeName, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title ;
?>
<div class="clients-address-update">
    
    <?= $this->render('_addressForm', [
        'model' => $model,
        'citiesList' => $citiesList,
    ]) ?>

</div>
