<?php

/* @var $this yii\web\View */

$this->title = 'Изменить операцию';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Касса комплектовщика',
];
$this->params['breadcrumbs'][] = [
    'url' => ['history'],
    'label' => 'История операций',
];
$this->params['breadcrumbs'][] = $this->title;


$renderParams = $this->context->getRenderParamsForTransactUpdate(
    $pickercashTransactModel, 
    $banknotesModel
);


?>
<div class="cashdesks-pickercash-transactUpdate">
    
    <?= $this->render('../common/_pickercashTransactUpdateForm', $renderParams) ?>
    
</div>