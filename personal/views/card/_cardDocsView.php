<?php

use yii\helpers\Html;
use himiklab\colorbox\Colorbox;

?>

<?= Colorbox::widget([
    'targets' => [
        '.colorbox' => [
        ],
    ],
    
]) ?>

<?php $img = Html::img(
    $cardDocsModel->getFileUrl($documentsUrl), 
    ['style' => 'max-width : 800px; max-height : 600px;'] ); 
?>

<?= Html::a($img, $cardDocsModel->getFileUrl($documentsUrl), ['class' => 'colorbox', 'title' => 'Полный размер']);