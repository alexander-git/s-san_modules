<?php

/* @var $this yii\web\View */

$this->title = 'Изменить операцию';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'История',
];
$this->params['breadcrumbs'][] = [
    'url' => ['pickercash-history'],
    'label' => 'Касса комплектовщика',
];
$this->params['breadcrumbs'][] = $this->title;


$renderParams = $this->context->getRenderParamsForPickercashTransactUpdate(
    $pickercashTransactModel, 
    $banknotesModel
);

?>
<div class="cashdesks-history-pickercashTransactUpdate">
    
    <?= $this->render('../common/_pickercashTransactUpdateForm', $renderParams) ?>
   
</div>