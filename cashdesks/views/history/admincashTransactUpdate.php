<?php

/* @var $this yii\web\View */

$this->title = 'Изменить операцию';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'История',
];
$this->params['breadcrumbs'][] = [
    'url' => ['admincash-history'],
    'label' => 'Сейф администратора',
];
$this->params['breadcrumbs'][] = $this->title;


$renderParams = $this->context->getRenderParamsForAdmincashTransactUpdate(
    $admincashTransactModel, 
    $banknotesModel
);

?>
<div class="cashdesks-history-admincashTransactUpdate">
    
    <?= $this->render('../common/_admincashTransactUpdateForm', $renderParams) ?>
   
</div>