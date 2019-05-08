<?php

/* @var $this yii\web\View */
/* @var $model app\modules\clients\models\Client */

$this->title = 'Добавить адрес';
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $clientModel->name, 'url' => ['view', 'id' => $clientModel->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clients-client-addressCreate">

    <?= $this->render('_addressForm', [
        'addressModel' => $addressModel,
        'clientAddressModel' => $clientAddressModel,
        'citiesList' => $citiesList,
    ]) ?>

</div>
