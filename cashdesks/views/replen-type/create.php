<?php

/* @var $this yii\web\View */
/* @var $model app\modules\cashdesks\models\ReplenType */

$this->title = 'Создать';
$this->params['breadcrumbs'] []= [
    'url' => ['service/index'],
    'label' => 'Управление'
];
$this->params['breadcrumbs'][] = [
    'label' => 'Виды пополнений', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-replenType-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
