<?php

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Question */

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Question */

$this->title = 'Обновить';
$this->params['breadcrumbs'][] = [
    'label' => 'Анкеты', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = [
    'label' => $questionModel->name,
    'url' => ['view', 'id' => $questionModel->id],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="personal-question-update">

    <?= $this->render('_form', [
        'questionModel' => $questionModel,
        'needDate' => false,
        'needHistoryText' => false,
        'buttonLabel' => 'Обновить',
    ]) ?>
        
</div>
