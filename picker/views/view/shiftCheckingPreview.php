<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Закрытие суточной смены';
$this->params['breadcrumbs'][] = [
    'url' => ['picker-index'], 
    'label' => 'Смена'
];
$this->params['breadcrumbs'][]= $this->title;

?>

<div class="picker-view-shiftChekingPreview">
    <?php if ($shiftsCourierOpenedCount > 0) : ?>
        <p class="alert alert-danger">
            Вы единственный комплектовщик в суточной смене. Перед тем как 
            приступить к закрытию суточной смены нужно закрыть все смены
            курьеров.
        </p>
        <?= Html::a('Назад', ['picker-index'], ['class' => 'btn btn-primary']) ?>
    <?php else : ?>
        <p class="alert alert-info">
            Вы единственный комплектовщик в суточной смене.
            Приступить к её закрытию?
        </p>
        <?= Html::a('Назад', ['picker-index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Далее', ['shift-checking-by-main-picker-start'], ['data-method' => 'post', 'class' => 'btn btn-success']) ?>
    <?php endif; ?>
</div>

