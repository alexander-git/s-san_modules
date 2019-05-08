<?php


/* @var $this yii\web\View */
/* @var $model app\modules\orders\models\PaymentType */

$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['settings/index']];
$this->params['breadcrumbs'][] = ['label' => 'Типы оплаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-paymentType-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
