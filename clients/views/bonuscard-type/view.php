<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\clients\models\BonuscardType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Типы бонусных карт', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clients-bonuscardType-view">
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
            'name',
            'discount',
            'bonusquan',
            'minmoney',
            'bonuscardsCount'
        ],
    ]) ?>

</div>
