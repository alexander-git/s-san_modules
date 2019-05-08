<?php

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Card */

$this->title = 'Создать документ ('.$docsListModel->name.')';

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
<div class="personal-card-cardDocsCreate">

    <?= $this->render('_docUploadForm', [
        'docUploadFormModel' => $docUploadFormModel,
        'isUpdate' => false,
    ]) ?>

</div>
