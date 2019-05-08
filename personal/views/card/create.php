<?php


/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Card */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = [
    'label' => 'Карточки сотрудников', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="personal-card-create">

    <?= $this->render('_cardForm', [
        'model' => $model,
    ]) ?>

</div>
