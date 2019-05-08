<?php

/* @var $this yii\web\View */

$this->title = 'Просмотр';

$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Сейф администратора',
];
$this->params['breadcrumbs'][] = [
    'url' => ['pickercash-transfer-index'],
    'label' => 'Переводы из касссы комплектовщика',
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-admincash-pickercashTransferView">    
    
    <?= $this->render('_transferFromPickercashView', [
        'model' => $model,
    ]); ?>
       
</div>
