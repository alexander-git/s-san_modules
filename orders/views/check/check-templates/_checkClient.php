<?php

use app\modules\orders\models\Order;
use app\modules\orders\models\OrdersApi;
use app\modules\orders\helpers\DateHelper;
use app\modules\orders\helpers\TimeHelper;

$DATETIME_FORMAT = ' H:i d.m.Y';
$DATE_FORMAT = 'd.m.Y';
$TIME_FORMAT = 'H:i';

$monthNumber = (int) DateHelper::convertDate($model->delivery_date, Order::DATE_FORMAT, 'n');
$monthShortRussianName = DateHelper::getMonthShortRussianName($monthNumber);
$deliveryDateTimeStr = TimeHelper::convertTime($model->delivery_time, Order::TIME_SHORT_FORMAT, $TIME_FORMAT)
    .' '.DateHelper::convertDate($model->delivery_date, Order::DATE_FORMAT, 'd')
    .$monthShortRussianName;
       
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
<div class="checkClient">
    
    <div class="checkClient__restaurantName borderDottedTop borderDottedBottom">
        Ресторан доставки "Сытый Сан"
    </div>
    
    <div class="checkClient__cityName">
        <?= $model->cityName ?>
    </div>

    <div class="checkClient__headerOrderNum">
        Заказ №<?= $model->order_num ?>
    </div>
    
    <div class="checkClient__headerDeliveryDate">
        <?= $deliveryDateTimeStr ?>
    </div>
        
    <div class="checkClient__clientInfo">
        <div class="checkClient__clientInfoLeft">
            Клиент<br />
            Тел<br />
            Адрес
        </div>
        <div class="checkClient__clientInfoRight">
            <br />
            <?= $model->phone ?><br />
            <?=$model->address ?>
        </div>
        <div class="clearBoth">
    </div>
       
    <div class="checkClient__personNum">
        <div class="checkClient__personNumLeft">
            Количество персон :
        </div>
        <div class="checkClient__pesonNumRight">
            <?=$model->person_num ?>
        </div>
        <div class="clearBoth">
    </div>
    
    <div class="checkClient__comment">
        <div class="checkClient__commentLeft">
            Примечание:
        </div>
        <div class="checkClient__commentRight">

        </div>
        <div class="clearBoth">   
    </div>
    
    <div class="checkClient__orderInfo">
        <div>
           Менеджер: <?=$model->userName ?>
        </div>
        <div>
           Курьер: 
        </div>
        <div>
            Заказ принят: <?= DateHelper::getDateStringFromTimestamp($dateAccept, $DATETIME_FORMAT); ?>
        </div>
        <div>
            Накладная оформлена: 
        </div>
    </div>
    <table class="checkClient__productsTable">
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
                <td><?=$orderItem->price.$moneySuffixRub?></td>
                <td><?=$orderItem->total_price.$moneySuffixRub?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="block borderDottedBottom">
        <div class="leftColumn">
            К оплате:
        </div>
        <div class="rightColumn">
            <?= $model->total_pay.$moneySuffixRub ?>
        </div>
        <div class="clearBoth"></div>
    </div>
    
    <div class="block borderDottedBottom">
        <div class="leftColumn">
            Наличные:
        </div>
        <div class="rightColumn">
            <?= $model->total_price.$moneySuffixRub ?>
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
    
    <div class="checkClient__summaryTotalPay">
        <div class="checkClient__summaryTotalPayLeft fontBold">
            Итого к оплате:
        </div>
        <div class="checkClient__summaryTotalPayRight fontBold">
            <?= $model->total_pay.$moneySuffixRub ?>
        </div>
        <div class="clearBoth"></div>
    </div>
    
    <div>
        Отметки о выполнении
    </div>
    <?php for ($i = 1; $i <= 4; $i++) : ?>
        <div class="checkClient__divTopLine"></div>
    <?php endfor; ?>
       
    <div class="checkClient__footerDeliveryDate">
        <?= $deliveryDateTimeStr ?>
    </div>

    <div class="checkClient__footerOrderNum">
        Заказ №<?= $model->order_num ?>
    </div>
</div>
