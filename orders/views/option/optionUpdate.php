<?php

$this->title = 'Обновить ('.$model->name.')';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['settings/index']];
$this->params['breadcrumbs'][] = ['label' => 'Основные настройки', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Список', 'url' => ['option-index']];
$this->params['breadcrumbs'][] = $this->title; 

?>
<div class="orders-option-optionUpdate">

    <?= $this->render('_optionForm', [
        'model' => $model,
    ]) ?>

</div>
