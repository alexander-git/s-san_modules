<?php

use yii\helpers\Html;
use yii\helpers\Url;

require_once __DIR__.'/_ids.php';
require_once __DIR__.'/_createUpdateFormJs.php';

?>

<?= Html::a('Создать', false, [
    'class' => 'showModalButton btn btn-success',
    'data-modal' => '#'.$modalId,
    'data-modal-header' => '#'.$modalHeaderId,
    'data-modal-content' => '#'.$modalContentId,
    'data-url' => Url::to(['create']),
    'data-title' => 'Создать',
]) ?>