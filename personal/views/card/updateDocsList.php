<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\personal\models\Vacancy */

$this->title = 'Обновить документы';
$this->params['breadcrumbs'][] = [
    'label' => 'Карточки сотрудников', 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = [
    'label' => $model->name, 
    'url' => ['view', 'id' => $model->id]
];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="personal-card-updateDocsList">

    <?php $form = ActiveForm::begin(); ?>

  
    <div class="form-group">
    
        <?php foreach ($cardDocsModels as $cardDocsModel): ?>
            <?php $id = $cardDocsModel->docs_id; ?>
            <?= $form->field($cardDocsModel, '['.$id.']check')
                ->checkbox([
                    'label' => $docsListDefaultModels[$id]->name
                ]);
            ?>
        <?php endforeach; ?>
        
        <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
