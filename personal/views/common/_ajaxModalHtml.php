<?php

use yii\bootstrap\Modal;
use app\modules\personal\assets\ModalAsset;

require_once __DIR__.'/_ids.php';

ModalAsset::register($this);
?>

<?php Modal::begin([
    'id' => $modalId,
    'headerOptions' => ['id' => $modalHeaderId],
    'size' => 'modal-md',
    'toggleButton' => false,
]); ?>
    <div id="<?=$modalContentId ?>"></div>
<?php Modal::end(); ?>
