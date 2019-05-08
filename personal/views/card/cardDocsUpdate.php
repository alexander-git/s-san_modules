<?php

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Card */

$this->title = 'Обновить документ ('.$docsListModel->name.')';

$this->params['breadcrumbs'][] = [
    'label' => 'Карточки сотрудников', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = [
    'label' => $cardModel->name, 
    'url' => ['view', 'id' => $cardModel->id]
];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="personal-card-cardDocsUpdate">

    <?= $this->render('_cardDocsView', [
        'cardDocsModel' => $cardDocsModel,
        'documentsUrl' => $documentsUrl,
    ]) ?>
    
    <br /><br /><br />
    
    <?= $this->render('_docUploadForm', [
        'docUploadFormModel' => $docUploadFormModel,
        'isUpdate' => true,
    ]) ?>

</div>
