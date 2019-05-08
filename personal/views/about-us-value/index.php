<?php

use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\widgets\Pjax;

/* @var $this yii\web\View */

$this->title = 'Как узнали о нас';
$this->params['breadcrumbs'][] = [
    'url' => ['settings/index'],
    'label' => 'Настройки',
];
$this->params['breadcrumbs'][] = $this->title;

require_once __DIR__.'/../common/_ids.php';
require_once __DIR__.'/../common/_createUpdateFormJs.php';

$actionColumn = require __DIR__.'/../common/_ajaxActionColumn.php';

?>
<div class="personal-aboutUsValue-index">

    <p>
       <?php require __DIR__.'/../common/_ajaxCreateButtonHtml.php' ?>
    </p>
    
  
    <?php require __DIR__.'/../common/_ajaxModalHtml.php' ?>
         
    <?php Pjax::begin([
        'id' => $gridViewContainerId,
    ]); ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::className()],
            'name',
            $actionColumn,
        ],
    ]); ?>
    
    <?php Pjax::end(); ?>
        
</div>
