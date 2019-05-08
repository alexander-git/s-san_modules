<?php

/* @var $this yii\web\View */

$this->title = 'Просмотр операции';

$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Сейф администратора',
];
$this->params['breadcrumbs'][] = [
    'url' => ['history'],
    'label' => 'История',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-admincash-transactView">  
    
    <?= $this->render('../common/_admincashTransactView', [
        'model' => $model,
        'showBeforeAcceptReject' => false,
    ]) ?>
       
</div>
