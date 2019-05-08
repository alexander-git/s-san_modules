<?php

/* @var $this yii\web\View */
/* @var $model app\modules\cashdesks\models\ReplenPurpose */

$this->title = 'Обновить('.$model->name.')';
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
<div class="cashdesks-replenPurpose-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
