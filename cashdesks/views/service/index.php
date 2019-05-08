<?php

use yii\helpers\Html;


/* @var $this yii\web\View */

$this->title = 'Управление';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashdesks-service-index">
    
    <p>
        <?= Html::a('Виды пополнений', ['replen-type/index'], ['class' => 'btn btn-primary']) ?>
    </p>
   
    <p>
        <?= Html::a('Виды расходов', ['expense-type/index'], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <p>
        <?= Html::a('Цели пополнений', ['replen-purpose/index'], ['class' => 'btn btn-primary']) ?>
    </p>
    
</div>
