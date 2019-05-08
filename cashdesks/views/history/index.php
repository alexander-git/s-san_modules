<?php

use yii\helpers\Html;


/* @var $this yii\web\View */

$this->title = 'История';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-history-index">
    
    <p>
        <?= Html::a('Сейф администратора', ['admincash-history'], ['class' => 'btn btn-primary']) ?>
    </p>
   
    <p>
        <?= Html::a('Касса комплектовщика', ['pickercash-history'], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <p>
        <?= Html::a('Под отчёт', ['accountable-history'], ['class' => 'btn btn-primary']) ?>
    </p>
    
</div>
