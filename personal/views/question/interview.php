<?php

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Question */

$this->title = 'Пригласить на собеседование';
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
<div class="personal-question-interview">

    <?= $this->render('_form', [
        'questionModel' => $questionModel,
        'questionHistoryModel' => $questionHistoryModel,
        'needDate' => true,
        'dateLabel' => 'Дата собеседования',
        'needHistoryText' => true,
        'buttonLabel' => 'Выполнить',
    ]) ?>
        
</div>
