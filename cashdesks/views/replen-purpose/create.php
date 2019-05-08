<?php

/* @var $this yii\web\View */
/* @var $model app\modules\cashdesks\models\ReplenPurpose */

$this->title = 'Создать';
$this->params['breadcrumbs'] []= [
    'url' => ['service/index'],
    'label' => 'Управление'
];
$this->params['breadcrumbs'][] = [
    'label' => 'Цели пополнений', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-replenPurpose-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
