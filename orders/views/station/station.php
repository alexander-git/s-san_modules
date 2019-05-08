<?php

use yii\web\View;
use yii\helpers\Url;
use app\modules\orders\assets\StationAsset;
use app\modules\orders\assets\MustacheAsset;


/* @var $this yii\web\View */

$this->title = $stationName;
$this->params['breadcrumbs'][] = [
    'label' => 'Станции ('.$cityName.')', 
    'url' => ['index', 'cityId' => $cityId]
];
$this->params['breadcrumbs'][] = $this->title; 

$getCardsUrlTemplate = Url::to([
    'get-cards', 
    'cityId' => '__cityId__',
    'stationId' => '__stationId__',
]);

$updateCardsUrlTemplate = Url::to([
    'update-cards',
    'cityId' => '__cityId__',
    'stationId' => '__stationId__',
]);

$startCardInWorkUrlTemplate = Url::to([
    'start-card-in-work',
    'orderId' => '__orderId__',
    'stationId' => '__stationId__',
]);

$completeCardUrlTemplate = Url::to([
    'complete-card',
    'orderId' => '__orderId__',
    'stationId' => '__stationId__',
]);

$cancelCardUrlTemplate = Url::to([
    'cancel-card',
    'orderId' => '__orderId__',
    'stationId' => '__stationId__',
]);

$setProductPreparedUrlTemplate = Url::to([
    'set-product-prepared',
    'orderId' => '__orderId__',
    'productId' => '__productId__',
]);

$setProductPreparingUrlTemplate = Url::to([
    'set-product-preparing',
    'orderId' => '__orderId__',
    'productId' => '__productId__',
]);

$stationParasmJs = <<<JS
    {
        'cityId' : $cityId,
        'stationId' : $stationId,
        'timeOffset' : $timeOffset,
        'stationOrdersCount' : $stationOrdersCount,
        'orderStageIds' : $orderStageIdsJson,
        'orderItemLogStates' : $orderItemLogStatesJson,
        'getCardsUrlTemplate' : '$getCardsUrlTemplate',
        'updateCardsUrlTemplate' : '$updateCardsUrlTemplate',
        'startCardInWorkUrlTemplate' : '$startCardInWorkUrlTemplate',
        'completeCardUrlTemplate' : '$completeCardUrlTemplate',
        'cancelCardUrlTemplate' : '$cancelCardUrlTemplate',
        'setProductPreparedUrlTemplate' : '$setProductPreparedUrlTemplate',
        'setProductPreparingUrlTemplate' : '$setProductPreparingUrlTemplate',
        'defaultCardClasses' : ['gray'],
        'inWorkCardClasses' : ['orange'],
        'canceledCardClasses' : ['red'],
    }
JS;

StationAsset::register($this);
MustacheAsset::register($this);

$this->registerJs("StationScript.init($stationParasmJs);", View::POS_READY);

?>
<div class="orders-station-station">
    
    <div class="stationTopPanel bg-info clearfix">
        <div class="pull-left">
            Станция №<?=$stationId?> - <?=$stationName?>
        </div>
        <div class="pull-right">
            Сейчас <span data-select="station__currentTime"></span>
        </div>
    </div>
    
    <div data-select="station__cardsContainer"></div>
        
    <script data-select="station__cardTemplate" type="x-tmpl-mustache">
        <div class="card" data-select-card="{{orderId}}" data-order-id="{{orderId}}">
            <div class="card__header" data-select="card__headerContainer">
            </div>
            <div class="card__body">
                <div class="card__bodyContent" data-select="card__bodyContainer">
                </div>
            </div>
            <div class="card__info" data-select="card__infoContainer">
            </div>
        </div>
    </script>

    <script data-select="station__headerDefaultTemplate" type="x-tmpl-mustache">
        <div class="card__headerContent">
            <span class="card__headerTime" title={{date}}>{{time}}</span>
        </div>
    </script>
    
    <script data-select="station__headerInWorkTemplate" type="x-tmpl-mustache">
        <div class="card__headerContent">
            <span class="card__headerTime" title={{date}}>{{time}}</span>
            - <span class="card__headerNumber">{{number}}</span>
        </div>
    </script>
    
    <script data-select="station__headerCanceledTemplate" type="x-tmpl-mustache">
        <div class="card__headerContent">
            <span class="card__headerTime" title={{date}}>{{time}}</span>
            {{#number}}
                - <span class="card__headerNumber">{{number}}</span>
            {{/number}}
        </div>
    </script>
    
    <script data-select="station__productNewTemplate" type="x-tmpl-mustache">
        <div class="card__product white" data-select-product="{{orderId}}-{{productId}}"  data-product-id="{{productId}}>
            <span class="card__productName">{{name}}</span>
            (<span class="card__productQuantity">{{quantity}}</span>)
        </div>
    </script>
        
    <script data-select="station__productPreparingTemplate" type="x-tmpl-mustache">
        <div class="card__product card__product--preparing white" data-select-product="{{orderId}}-{{productId}}" data-product-id="{{productId}}">
            <span class="card__productName">{{name}}</span>
            (<span class="card__productQuantity">{{quantity}}</span>)
        </div>
    </script>

    <script data-select="station__productPreparedTemplate" type="x-tmpl-mustache">
        <div class="card__product card__product--prepared green" data-select-product="{{orderId}}-{{productId}}" data-product-id="{{productId}}">
            <span class="card__productName">{{name}}</span>
            (<span class="card__productQuantity">{{quantity}}</span>)
        </div>
    </script>
    
    <script data-select="station__infoDefaultTemplate" type="x-tmpl-mustache">
        <div class="card__infoContent orange card__inWorkButton" data-select="card__inWorkButton">
            В работу
        </div>
    </script>
        
    <script data-select="station__infoPreparingTemplate" type="x-tmpl-mustache">
        <div class="card__infoContent card__infoContent orange">
            <span data-select="card__infoTimer"></span>
        </div>
    </script>
    
    <script data-select="station__infoPreparedTemplate" type="x-tmpl-mustache">
        <div class="card__infoContent card__completeButton green" data-select="card__completeButton">
            Готово
            <span data-select="card__infoTimer"></span>
        </div>
    </script>
    
    <script data-select="station__infoCanceledTemplate" type="x-tmpl-mustache">
        <div class="card__infoContent card__cancelButton red" data-select="card__cancelButton">
            Отменён
        </div>
    </script>
    
</div>
