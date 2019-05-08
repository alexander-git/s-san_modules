<?php

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Question */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = [
    'label' => 'Анкеты', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="personal-question-create">

    <?= $this->render('_form', [
        'questionModel' => $questionModel,
        'needDate' => false,
        'needHistoryText' => false,
        'buttonLabel' => 'Создать',
    ]) ?>
            
</div>
