<?php

$this->title = 'Создать';
$this->params['breadcrumbs'][] = [
    'url' => ['service/index'],
    'label' => 'Управление',
];
$this->params['breadcrumbs'][] = [
    'label' => 'Виды расходов', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-expenseType-typeCreate">

    <?= $this->render('_typeForm', ['model' => $model]) ?>

</div>
