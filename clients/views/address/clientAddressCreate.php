<?php

/* @var $this yii\web\View */

$this->title = 'Добавить клиента';
$this->params['breadcrumbs'][] = ['label' => 'Адреса', 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $addressModel->compositeName, 
    'url' => ['view', 'id' => $addressModel->id]
];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="clients-address-clientAddressCreate">

    <?= $this->render('_clientAddressForm', [
        'clientAddressModel' => $clientAddressModel,
        'clientsList' => $clientsList,
    ]) ?>

    
</div>
