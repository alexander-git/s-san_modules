<?php

/* @var $this yii\web\View */
/* @var $model app\modules\clients\models\BonuscardType */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Типы бонусных карт', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clients-bonuscardType-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
