<?php

$this->title = 'Обновить заказы клиента';
$this->params['breadcrumbs'][] = ['label' => 'Адреса', 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $addressModel->compositeName, 
    'url' => ['view', 'id' => $addressModel->id]
];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="clients-address-clientAddressUpdate">

    <?= $this->render('_clientAddressForm', [
        'clientAddressModel' => $clientAddressModel,
        'clientModel' => $clientModel,
    ]) ?>

</div>
