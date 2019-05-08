<?php

/* @var $this yii\web\View */

$this->title = 'Просмотр операции';

$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Под отчёт',
];
$this->params['breadcrumbs'][] = [
    'url' => ['history'],
    'label' => 'История',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-accountable-transactView">    
    <?= $this->render('../common/_accountableTransactView', [
        'model' => $model
    ]) ?>
       
</div>