<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\modules\orders\assets\StationPickAsset;
use app\modules\orders\assets\MustacheAsset;

/* @var $this yii\web\View */

$this->title = $stationName;
$this->params['breadcrumbs'][] = [
    'label' => 'Станции ('.$cityName.')', 
    'url' => ['index', 'cityId' => $cityId]
];
$this->params['breadcrumbs'][] = $this->title; 

$getCardsUrlTemplate = Url::to([
    'get-cards-pick',
    'cityId' => '__cityId__'
]);

$updateCardsUrlTemplate = Url::to([
    'update-cards-pick',
    'cityId' => '__cityId__'   
]);

$startCardInPickUrlTemplate = Url::to([
    'start-card-in-pick',
    'orderId' => '__orderId__',
]);

$cancelCardUrlTemplate = Url::to([
    'cancel-card-pick',
    'orderId' => '__orderId__',
]);

$deliverCardUrlTemplate = Url::to([
    'deliver-card-pick',
    'orderId' => '__orderId__',
]);

$setProductAddedUrlTemplate = Url::to([
    'set-product-added',
    'orderId' => '__orderId__',
    'productId' => '__productId__',
]);

$checksPrintUrlTemplate = Url::to([
    'check/checks-print',
    'orderId' => '__orderId__',
]);

$stationPickParasmJs = <<<JS
    {
        'cityId' : $cityId,
        'stationId' : $stationId,
        'timeOffset' : $timeOffset,
        'orderStageIds' : $orderStageIdsJson,
        'orderItemLogStates' : $orderItemLogStatesJson,
        'getCardsUrlTemplate' : '$getCardsUrlTemplate',
        'updateCardsUrlTemplate' : '$updateCardsUrlTemplate',
        'startCardInPickUrlTemplate' : '$startCardInPickUrlTemplate',
        'deliverCardUrlTemplate' : '$deliverCardUrlTemplate',
        'cancelCardUrlTemplate' : '$cancelCardUrlTemplate',
        'setProductAddedUrlTemplate' : '$setProductAddedUrlTemplate',
        'checksPrintUrlTemplate' : '$checksPrintUrlTemplate',
        'defaultCardClasses' : ['blue'],
        'inPickCardClasses' : ['orange'],
        'canceledCardClasses' : ['red'],
    }
JS;

StationPickAsset::register($this);
MustacheAsset::register($this);

$this->registerJs("StationPickScript.init($stationPickParasmJs);", View::POS_READY);

?>
<div class="orders-station-stationPick">
    
    <p>
        <?= Html::a('В Работе', ['station-pick', 'cityId' => $cityId], ['class' => 'btn btn-success']); ?>
        <?= Html::a('Готовые', ['index-ready', 'cityId' => $cityId], ['class' => 'btn btn-primary']); ?>
    </p>
    
    <div class="stationTopPanel bg-info clearfix">
        <div class="pull-left">
            Станция №<?=$stationId?> - <?=$stationName?>
        </div>
        <div class="pull-right">
            Сейчас <span data-select="stationPick__currentTime"></span>
        </div>
    </div>
    
    <div data-select="stationPick__cardsContainer"></div>
    
    <script data-select="stationPick__cardTemplate" type="x-tmpl-mustache">
        <div class="cardPick" data-select-card-pick="{{orderId}}" data-order-id="{{orderId}}">
            <div class="cardPick__header" data-select="cardPick__headerContainer">
            </div>
            <div class="cardPick__body">
                <div class="cardPick__bodyContent">
                    <div class="cardPick__bodyContentContainer" data-select="cardPick__bodyContainer">
                    </div>
                </div>
            </div>
            <div class="cardPick__footerTime" data-select="cardPick__footerTimeContainer">
            </div>
            <div class="cardPick__footerButton" data-select="cardPick__footerButtonContainer">
            </div>
        </div>
    </script>

    <script data-select="stationPick__headerTemplate" type="x-tmpl-mustache">
        <div class="cardPick__headerContent">
            <div class="clearfix">
                <div class="cardPick__headerOrderNum pull-left">
                № {{orderNum}}
                </div>
                <div class="cardPick__headerTime pull-right">
                    <span title={{date}}>{{time}}</span>
                </div>
            </div>
        </div>
    </script>
      
    <script data-select="stationPick__productKitchenNewTemplate" type="x-tmpl-mustache">
        <div class="cardPick__productKitchen white" data-select-product="{{orderId}}-{{productId}}"  data-product-id="{{productId}}">
            <span class="cardPick__productName">{{name}}</span>
            (<span class="cardPick__productQuantity">{{quantity}}</span>)
        </div>
    </script>
        
    <script data-select="stationPick__productKitchenPreparingTemplate" type="x-tmpl-mustache">
        <div class="cardPick__productKitchen white" data-select-product="{{orderId}}-{{productId}}"  data-product-id="{{productId}}">
            <span class="cardPick__productNumberOnStation orangeIcon">{{number}}</span> 
            <span class="cardPick__productName">{{name}}</span>
            (<span class="cardPick__productQuantity">{{quantity}}</span>)
        </div>
    </script>
    
    <script data-select="stationPick__productKitchenPreparedTemplate" type="x-tmpl-mustache">
        <div class="cardPick__productKitchen white" data-select-product="{{orderId}}-{{productId}}"  data-product-id="{{productId}}">
            <span class="cardPick__productNumberOnStation orangeIcon">{{number}}</span> 
            <span class="cardPick__productName">{{name}}</span>
            (<span class="cardPick__productQuantity">{{quantity}}</span>)
        </div>
    </script>
    
    <script data-select="stationPick__productKitchenCompleteTemplate" type="x-tmpl-mustache">
        <div class="cardPick__productKitchen cardPick__productKitchen--complete orange" data-select-product="{{orderId}}-{{productId}}"  data-product-id="{{productId}}">
            <span class="cardPick__productNumberOnStation redIcon">{{number}}</span> 
            <span class="cardPick__productName">{{name}}</span>
            (<span class="cardPick__productQuantity">{{quantity}}</span>)
        </div>
    </script>
    
    <script data-select="stationPick__productKitchenAddedTemplate" type="x-tmpl-mustache">
        <div class="cardPick__productKitchen cardPick__productKitchen--added green" data-select-product="{{orderId}}-{{productId}}"  data-product-id="{{productId}}">
            <span class="cardPick__productName">{{name}}</span>
            (<span class="cardPick__productQuantity">{{quantity}}</span>)
        </div>
    </script>
    
    <script data-select="stationPick__productKitchenCanceledTemplate" type="x-tmpl-mustache">
        <div class="cardPick__productKitchen cardPick__productKitchen--canceled white" data-select-product="{{orderId}}-{{productId}}"  data-product-id="{{productId}}">
            <span class="cardPick__productName">{{name}}</span>
            (<span class="cardPick__productQuantity">{{quantity}}</span>)
        </div>
    </script>
    
    <script data-select="stationPick__pickLabelTemplate" type="x-tmpl-mustache">
        <div class="cardPick__pickLabel">
            Собрать:
        </div>
    </script>
    
    <script data-select="stationPick__productPickNewTemplate" type="x-tmpl-mustache">
        <div class="cardPick__productPick white" data-select-product="{{orderId}}-{{productId}}"  data-product-id="{{productId}}>
            <span class="cardPick__productName">{{name}}</span>
            (<span class="cardPick__productQuantity">{{quantity}}</span>)
        </div>
    </script>
        
    <script data-select="stationPick__productPickPreparingTemplate" type="x-tmpl-mustache">
        <div class="cardPick__productPick cardPick__productPick--preparing white" data-select-product="{{orderId}}-{{productId}}"  data-product-id="{{productId}}>
            <span class="cardPick__productName">{{name}}</span>
            (<span class="cardPick__productQuantity">{{quantity}}</span>)
        </div>
    </script>
    
    <script data-select="stationPick__productPickAddedTemplate" type="x-tmpl-mustache">
        <div class="cardPick__productPick cardPick__productPick--addedd green" data-select-product="{{orderId}}-{{productId}}"  data-product-id="{{productId}}>
            <span class="cardPick__productName">{{name}}</span>
            (<span class="cardPick__productQuantity">{{quantity}}</span>)
        </div>
    </script>
    
     <script data-select="stationPick__personNumTemplate" type="x-tmpl-mustache">
        <div class="cardPick__personNum">
            Набор для {{personNum}} персон.
        </div>
    </script>
    
    <script data-select="stationPick__footerTimeTemplate" type="x-tmpl-mustache">
        <div class="card__footerTimeContent">
            Время в работе: <span data-select="cardPick__timer">00:00</span>
        </div>
    </script>
    
    <script data-select="stationPick__buttonStartPickEnabledTemplate" type="x-tmpl-mustache">
        <div class="cardPick__footerButtonContent cardPick__startPickButton orange" data-select="cardPick__startPickButton">
            Начать сборку
        </div>
    </script>
    
    <script data-select="stationPick__buttonStartPickDisabledTemplate" type="x-tmpl-mustache">
        <div class="cardPick__footerButtonContent cardPick__startPickButton  cardPick__startPickButton--disabled gray" data-select="cardPick__startPickButton">
            Начать сборку
        </div>
    </script>    
    
    <script data-select="stationPick__buttonDeliverEnabledTemplate" type="x-tmpl-mustache">
        <div class="cardPick__footerButtonContent cardPick__deliverButton green" data-select="cardPick__deliverButton">
            Доставить
        </div>
    </script>
    
    <script data-select="stationPick__buttonDeliverDisabledTemplate" type="x-tmpl-mustache">
        <div class="cardPick__footerButtonContent cardPick__deliverButton  cardPick__deliverButton--disabled gray" data-select="cardPick__deliverButton">
            Доставить
        </div>
    </script>
    
    <script data-select="stationPick__buttonCancelEnabledTemplate" type="x-tmpl-mustache">
        <div class="cardPick__footerButtonContent cardPick__cancelButton red" data-select="cardPick__cancelButton">
            Отменён
        </div>
    </script>
    
    <script data-select="stationPick__buttonCancelDisabledTemplate" type="x-tmpl-mustache">
        <div class="cardPick__footerButtonContent cardPick__cancelButton  cardPick__cancelButton--disabled gray" data-select="cardPick_cancelButton">
            Отменён
        </div>
    </script>         
    
</div>
