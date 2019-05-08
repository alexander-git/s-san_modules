<?php


/* @var $this yii\web\View */

$this->title = 'Просмотр операции';

$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Касса комплектовщика',
];
$this->params['breadcrumbs'][] = [
    'url' => ['history'],
    'label' => 'История',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-pickercash-transactView">   
    
    <?= $this->render('../common/_pickercashTransactView', [
        'model' => $model
    ])?>
       
</div>
