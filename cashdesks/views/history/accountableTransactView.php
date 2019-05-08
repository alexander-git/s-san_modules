<?php

/* @var $this yii\web\View */

$this->title = 'Просмотр операции';

$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'История',
];
$this->params['breadcrumbs'][] = [
    'url' => ['accountable-history'],
    'label' => 'Под отчёт',
];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-history-accountableTransactView">    
    
    <?= $this->render('../common/_accountableTransactView', [
        'model' => $model,
    ]) ?>
       
</div>
