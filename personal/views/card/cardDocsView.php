<?php


/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Card */

$this->title = $cardDocsModel->docsList->name;
$this->params['breadcrumbs'][] = [
    'label' => 'Карточки сотрудников', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = [
    'label' => $cardDocsModel->card->name, 
    'url' => ['view', 'id' => $cardDocsModel->card_id]
];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="personal-card-cardDocsView">

    <?= $this->render('_cardDocsView', [
        'cardDocsModel' => $cardDocsModel,
        'documentsUrl' => $documentsUrl,
    ]) ?>

</div>
