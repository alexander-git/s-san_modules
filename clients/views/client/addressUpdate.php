<?php

$this->title = 'Обновить адрес ('.$addressModel->compositeName .')';
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $clientModel->name, 'url' => ['view', 'id' => $clientModel->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clients-client-addressUpdate">

    <?= $this->render('_addressForm', [
        'addressModel' => $addressModel,
        'clientAddressModel' => $clientAddressModel,
        'citiesList' => $citiesList,
    ]) ?>

</div>