<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */

$this->title = 'Изменить операцию';
$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Сейф администратора',
];
$this->params['breadcrumbs'][] = [
    'url' => ['history'],
    'label' => 'История операций',
];
$this->params['breadcrumbs'][] = $this->title;

$renderParams = $this->context->getRenderParamsForTransactUpdate(
    $admincashTransactModel, 
    $banknotesModel
);

?>
<div class="cashdesks-admincash-transactUpdate">
    
    <?= $this->render('../common/_admincashTransactUpdateForm', $renderParams) ?>
    
</div>