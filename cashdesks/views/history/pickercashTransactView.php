<?php

/* @var $this yii\web\View */

$this->title = 'Просмотр операции';

$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'История',
];
$this->params['breadcrumbs'][] = [
    'url' => ['pickercash-history'],
    'label' => 'Касса комплектовщика',
];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-history-pickercashTransactView">    
    
    <?= $this->render('../common/_pickercashTransactView', [
        'model' => $model,
    ]) ?>
       
</div>
