<?php

/* @var $this yii\web\View */
/* @var $model app\modules\clients\models\BonuscardType */

$this->title = 'Обновить';
$this->params['breadcrumbs'][] = ['label' => 'Типы бонусных карт', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clients-bonuscardType-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
