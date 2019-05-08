<?php

/* @var $this yii\web\View */

$this->title = 'Просмотр операции';

$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'История',
];
$this->params['breadcrumbs'][] = [
    'url' => ['admincash-history'],
    'label' => 'Сейф администратора',
];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-history-admincashTransactView">    
    
    <?= $this->render('../common/_admincashTransactView', [
        'model' => $model,
    ]) ?>
       
</div>
