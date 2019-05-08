<?php

/* @var $this yii\web\View */
/* @var $model app\modules\orders\models\Stage */

$this->title = 'Обновить';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['settings/index']];
$this->params['breadcrumbs'][] = ['label' => 'Стадии заказов', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-stage-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
