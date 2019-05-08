<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use app\modules\picker\helpers\TotalHelper;

/* @var $this yii\web\View */

$this->title = 'Итог';

$this->params['breadcrumbs'][] = [
    'url' => ['shift-index'], 
    'label' => 'Суточная смена'
];
$this->params['breadcrumbs'][] = [
    'url' => ['shift-close-fill'],
    'label' => 'Ввод данных'
    
];
$this->params['breadcrumbs'][]= $this->title;


$countOrderTotal = TotalHelper::getTotal($dataProvider->models, 'count_order');
$checkSumTotal = TotalHelper::getTotal($dataProvider->models, 'check_sum');
$cashTotal = TotalHelper::getTotal($dataProvider->models, 'cash');
$checkNocashTotal = TotalHelper::getTotal($dataProvider->models, 'check_nocash');
$spendTotal = TotalHelper::getTotal($dataProvider->models, 'spend');
$giftsTotal = TotalHelper::getTotal($dataProvider->models, 'gifts');

$countOrderDiff = $shiftsModel->prog_check_count - $countOrderTotal;
$checkSumDiff = $shiftsModel->prog_turn - $checkSumTotal;
$cashDiff = $shiftsModel->turn_cashdesk - $cashTotal;
$checkNocashDiff = $shiftsModel->prog_turn_nocash - $checkNocashTotal;

$warningMessage = '';

if ($countOrderDiff !== 0) {
    $word = $countOrderDiff > 0 ? 'больше' : 'меньше';
    $value = abs($countOrderDiff);
    $warningMessage .= "Итоговое кол-во заказов $word расчётного на $value. <br/>";
}
if ($checkSumDiff  !== 0) {
    $word = $checkSumDiff  > 0 ? 'больше' : 'меньше';
    $value = abs($checkSumDiff);
    $warningMessage .= "Итоговый оборот $word расчётного на $value. <br/>";
}
if ($cashDiff !== 0) {
    $word = $cashTotal  > 0 ? 'больше' : 'меньше';
    $value = abs($cashTotal);
    $warningMessage .= "Итоговая сумма наличных $word расчётной на $value. <br/>";
}
if ($checkNocashDiff !== 0) {
    $word = $checkNocashDiff > 0 ? 'больше' : 'меньше';
    $value = abs($checkNocashDiff);
    $warningMessage .= "Итоговая сумма безнала $word расчётной на $value. <br/>";
}

?>
<div class="picker-view-shiftCloseSummary">

    <h3>Все смены курьеров</h3>
   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'showFooter' => true,        
        'footerRowOptions' => [
            'style' => 'font-weight : bold;'
        ],
        'columns' => [
            /*
            [
                'attribute' => 'date_open',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
                'filter' => false,
            ],
            
            [
                'attribute' => 'date_close',
                'format' => ['datetime', 'php:d-m-Y H:i:s'],
                'filter' => false,
            ],    
            */
            
            'courier_name',  
            
            [
                'attribute' => 'count_order',
                'footer' => $countOrderTotal
            ],
            
            [
                'attribute' => 'check_sum',
                'footer' => $checkSumTotal
            ],
             
            [
                'attribute' => 'cash',
                'footer' => $cashTotal,
            ],
            
            [
                'attribute' => 'check_nocash',
                'footer' => $checkNocashTotal,
            ],
            
            [
                'attribute' => 'spend',
                'footer' => $spendTotal,
            ],
            
            [
                'attribute' => 'gifts',
                'footer' => $giftsTotal,
            ],
        ],
    ]); ?>  
    

    <?php if (!empty($warningMessage)) : ?>
    <div class="alert alert-danger">
        <?= $warningMessage ?>
        <br /> 
        Вы можете указать причину несоответствия в комментарии ниже 
        и закрыть суточную смену, либо вернуться к редактированию данных.
    </div>
    <?php else : ?>
    <div class="alert alert-success">
        Итоговые данные за суточную смену совпадают с расчётными.
    </div>
    <?php endif; ?>
    
    
    <?php $form = ActiveForm::begin() ?>
        <?= $form->field($shiftsModel, 'message')->textInput(['maxlength' => true]); ?>
        <div class="form-group">
            <?= Html::a('Назад', ['shift-close-fill'], ['class' => 'btn btn-primary']); ?>
            <?= Html::a('Редактировать смены курьеров', ['shift-index'], ['class' => 'btn btn-primary']); ?>
            <?= Html::submitButton('Закрыть суточню смену', ['class' => 'btn btn-success', 'name'=> 'submitButton']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>