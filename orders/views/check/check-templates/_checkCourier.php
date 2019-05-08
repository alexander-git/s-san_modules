<?php

use app\modules\orders\models\Order;
use app\modules\orders\models\OrdersApi;
use app\modules\orders\helpers\DateHelper;
use app\modules\orders\helpers\TimeHelper;


$DATETIME_FORMAT = 'd.m.Y H:i';
$DATE_FORMAT = 'd.m.Y';
$TIME_FORMAT = 'H:i';

$deliveryDateTimeStr = DateHelper::convertDate($model->delivery_date, Order::DATE_FORMAT, $DATE_FORMAT)
    .' '.TimeHelper::convertTime($model->delivery_time, Order::TIME_SHORT_FORMAT, $TIME_FORMAT);
       
$moneySuffix = '.00';
$moneySuffixRub = '.00р.';

$expirationInterval = new DateInterval('PT3H');
$expirationDatetime =  (new \DateTime())
    ->setTimestamp($datePreparation)
    ->add($expirationInterval);

// Остаток на бонусном счёте.
$bonuses = null;
if ($model->client_id !== null) {
    $bonuscard = OrdersApi::getBonuscardByClientId($model->client_id);
    if ($bonuscard !== null) {
        $bonuses  = $bonuscard->bonuses.$moneySuffix;
    }
}

?>
<div class="checkCourier">
    <div class="checkCourier__headInfo">
        Скидка при cамовывозе<br />
        Забери заказ сам и получи на<br />
        него скидку!<br />
        Подробнее<br />
        www.s-san.ru
    </div>
    <div class="checkCourier__orderInfo">
        <div>
            Время приема заказа: <?= DateHelper::getDateStringFromTimestamp($dateAccept, $DATETIME_FORMAT); ?>
        </div>
        <div>
            Время приготовления заказа: <?= DateHelper::getDateStringFromTimestamp($datePreparation, $DATETIME_FORMAT); ?>
        </div>
        <div>
            Время доставки заказа: <?=$deliveryDateTimeStr?>
        </div>
        <div>
            Заказ: <?=$model->order_num ?>
        </div>
        <div>
            Количество персон: <?=$model->person_num?>
        </div>
    </div>
    <table class="checkCourier__productsTable">
        <thead>
            <tr class="borderDottedBottom">
                <td>Наименование</td>
                <td>Количество</td>
                <td>Цена</td>
                <td>Сумма</td>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($model->orderItems as $orderItem) : ?>
            <?php
                $product = $products[$orderItem->product_id];
                if (!empty($product->parent_id)) {
                    $product = $products[$product->parent_id];
                }
                $productName = $product->name;
            ?>
            <tr>
                <td><?=$productName?></td>
                <td><?=$orderItem->quantity?></td>
                <td><?=$orderItem->price.$moneySuffix?></td>
                <td><?=$orderItem->total_price.$moneySuffixRub?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="block borderDottedBottom">
        <div class="leftColumn">
            Итого к оплате:
        </div>
        <div class="rightColumn fontBold">
            <?= $model->total_pay.$moneySuffixRub ?>
        </div>
        <div class="clearBoth"></div>
    </div>
    
    <div class="block borderDottedBottom">
        <div class="leftColumn">
            Итого к оплате:
        </div>
        <div class="rightColumn">
            <?= $model->total_price.$moneySuffixRub ?>
        </div>
        <div class="clearBoth"></div>
    </div>
    
    <div class="block borderDottedBottom">
        <div class="leftColumn">
            Итого скидка:
        </div>
        <div class="rightColumn">
            <?= $model->tax.$moneySuffixRub ?>
        </div>
        <div class="clearBoth"></div>
    </div>
    
    <div class="block borderDottedBottom">
        <div class="leftColumn">
            Cкидка c бонусного счёта:
        </div>
        <div class="rightColumn">
            0
        </div>
        <div class="clearBoth"></div>
    </div>
    
    <div class="block borderDottedBottom">
        <div class="leftColumn">
            Остаток на бонусном счёте:
        </div>
        <div class="rightColumn">
            <?=$bonuses ?>
        </div>
        <div class="clearBoth"></div>
    </div>
    
    <div class="block">
        <div class="leftColumn">
            Итого к оплате:
        </div>
        <div class="rightColumn fontBold">
            <?= $model->total_pay.$moneySuffixRub ?>
        </div>
        <div class="clearBoth"></div>
    </div>
    
    <div class="checkCourier__restaurantName borderDottedTop borderDottedBottom">
        Ресторан доставки "Сытый Сан"
    </div>
    
    <div class="checkCourier__expirationDate">
        Годен до: <?=$expirationDatetime->format($DATETIME_FORMAT); ?>
    </div>
    
    <div class="checkCourier__expirationDateInfo">
        Срок годности при t+2+4C<br/>
        не более 3-ех часов<br/>
        с момента приготовления заказа
    </div>
    
    <div class="checkCourier__footerInfo textCenter">
        ВСЕ СУММЫ В РУБЛЯХ<br />
        СПАСИБО ПРИЯТНОГО АППЕТИТА
    </div>
</div>
