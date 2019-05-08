<?php

/* @var $this yii\web\View */
/* @var $model app\modules\clients\models\Address */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Адреса', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clients-address-create">

    <?= $this->render('_addressForm', [
        'model' => $model,
        'citiesList' => $citiesList,
    ]) ?>

</div>
