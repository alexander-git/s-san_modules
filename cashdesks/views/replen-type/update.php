<?php

/* @var $this yii\web\View */
/* @var $model app\modules\cashdesks\models\ReplenType */

$this->title = 'Обновить('.$model->name.')';
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
<div class="cashdesks-replenType-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
