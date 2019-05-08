<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Vacancy */

$this->title = $model->postName;
$this->params['breadcrumbs'][] = [
    'label' => 'Вакансии', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="personal-vacancy-view">
    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'post_id',
                'value' => $model->postName,
            ],
            [
                'attribute' => 'user_id',
                'value' => $model->userName,
            ],
            [
                'attribute' => 'depart_id',
                'value' => $model->departmentName,
            ],
            [
                'attribute' => 'state',
                'value' => $model->stateName,
            ],
            [
                'attribute' => 'date_create',
                'format' => ['date', 'php:d-m-Y H:i:s'],
            ],
            [
                'attribute' => 'date_public',
                'format' => ['date', 'php:d-m-Y H:i:s'],
            ],
            'text:ntext',
        ],
    ]) ?>

</div>
