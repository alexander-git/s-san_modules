<?php

/* @var $this yii\web\View */

$this->title = 'Изменить операцию';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'История',
];
$this->params['breadcrumbs'][] = [
    'url' => ['accountable-history'],
    'label' => 'Касса "под отчёт"',
];
$this->params['breadcrumbs'][] = $this->title;


$renderParams = $this->context->getRenderParamsForAccountableTransactUpdate(
    $accountableTransactModel
);

?>
<div class="cashdesks-history-admincashTransactUpdate">
    
    <?= $this->render('../common/_accountableTransactUpdateForm', $renderParams) ?>
   
</div>