<?php

use yii\helpers\Html;


/* @var $this yii\web\View */

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="personal-settings-index">
    
    <p>
        <?= Html::a('Должности', ['settings-post/index'], ['class' => 'btn btn-primary']) ?>
    </p>
   
    <p>
        <?= Html::a('Как узнали о нас', ['about-us-value/index'], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <p>
        <?= Html::a('Документы', ['docs-list/index'], ['class' => 'btn btn-primary']) ?>
    </p>
    
</div>
