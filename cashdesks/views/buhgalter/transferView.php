<?php

/* @var $this yii\web\View */

$this->title = 'Просмотр';

$this->params['breadcrumbs'][] = [
    'url' => ['index'],
    'label' => 'Переводы от администратора',
];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-buhgalter-transferView">    
    
    <?= $this->render('../common/_admincashTransactView', [
        'model' => $model,
    ]) ?>
    
</div>
