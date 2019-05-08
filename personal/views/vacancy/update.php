<?php

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Vacancy */

$this->title = 'Обновить';
$this->params['breadcrumbs'][] = [
    'label' => 'Вакансии', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = [
    'label' => $model->postName, 
    'url' => ['view' , 'id' => $model->id]
];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="personal-vacancy-update">

    <?= $this->render('_form', [
        'model' => $model,
        'settingsPostsList' => $settingsPostsList,
        'departmentsList' => $departmentsList,
    ]) ?>

</div>
