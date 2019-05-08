<?php

/* @var $this yii\web\View */
/* @var $model app\modules\clients\models\Client */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clients-client-create">

    <?= $this->render('_clientForm', [
        'model' => $model,
    ]) ?>

</div>
