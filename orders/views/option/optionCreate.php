<?php

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['settings/index']];
$this->params['breadcrumbs'][] = ['label' => 'Основные настройки', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Список', 'url' => ['option-index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="orders-option-optionCreate">

    <?= $this->render('_optionForm', [
        'model' => $model,
    ]) ?>

</div>
