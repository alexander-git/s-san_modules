<?php

/* @var $this yii\web\View */
/* @var $model app\modules\clients\models\Client */

$this->title = 'Обновить бонусную карту';
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $clientModel->name, 'url' => ['view', 'id' => $clientModel->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clients-client-bonuscardUpdate">

    <?= $this->render('_bonuscardForm', [
        'model' => $bonuscardModel,
        'bonuscardTypesList' => $bonuscardTypesList,
    ]) ?>

</div>
