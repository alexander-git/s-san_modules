<?php

$this->title = 'Обновить('.$model->id.')';
$this->params['breadcrumbs'][] = [
    'label' => 'Настройки', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = [
    'label' => 'Список настроек', 
    'url' => ['option-index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="picker-options-optionUpdate">

    <?= $this->render('_optionForm', [
        'model' => $model,
    ]) ?>

</div>
