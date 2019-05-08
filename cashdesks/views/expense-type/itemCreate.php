<?php

$this->title = 'Добавить элемент';
$this->params['breadcrumbs'][] = [
    'url' => ['service/index'],
    'label' => 'Управление',
];
$this->params['breadcrumbs'][] = [
    'label' => 'Виды расходов', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = [
    'label' => 'Обновить('.$expenseTypeModel->name.')', 
    'url' => ['type-update', 'id' => $expenseTypeModel->id]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-expenseType-itemCreate">

    <?= $this->render('_itemForm', ['expenseTypeItemModel' => $expenseTypeItemModel]) ?>
    
</div>
