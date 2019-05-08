<?php

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Card */

$this->title = 'Обновить';
$this->params['breadcrumbs'][] = [
    'label' => 'Карточки сотрудников', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = [
    'label' => $model->name, 
    'url' => ['view', 'id' => $model->id]
];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="personal-card-update">

    <?= $this->render('_cardForm', [
        'model' => $model,
    ]) ?>

</div>
