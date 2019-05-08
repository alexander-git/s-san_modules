<?php

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Vacancy */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = [
    'label' => 'Вакансии', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="personal-vacancy-create">

    <?= $this->render('_form', [
        'model' => $model,
        'settingsPostsList' => $settingsPostsList,
        'departmentsList' => $departmentsList,
    ]) ?>

</div>
