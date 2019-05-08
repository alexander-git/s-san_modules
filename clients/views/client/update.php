<?php

/* @var $this yii\web\View */
/* @var $model app\modules\clients\models\Client */

$this->title = 'Обновить';
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="clients-client-update">

    <?= $this->render('_clientForm', [
        'model' => $model,
    ]) ?>

</div>
