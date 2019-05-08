<?php

/* @var $this yii\web\View */
/* @var $model app\modules\orders\models\Stage */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['settings/index']];
$this->params['breadcrumbs'][] = ['label' => 'Стадии заказов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-stage-create">
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
