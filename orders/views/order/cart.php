<?php

use yii\web\View;
use yii\helpers\Html;
use app\modules\orders\assets\CartAsset;
use app\modules\orders\assets\OrderCssAsset;

/* @var $this yii\web\View */

$this->title = 'Корзина';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $orderModel->id, 'url' => ['view', 'orderId' => $orderModel->id]];
$this->params['breadcrumbs'][] = $this->title;

CartAsset::register($this);
OrderCssAsset::register($this);

$this->registerJs("CartScript.init();", View::POS_READY);
?>
<div class="orders-order-cart">

    <?= $this->render('_orderMenu', ['orderModel' => $orderModel]) ?>

    <div class="row topPanel">
        <div class="col-md-4">
            <div class="bg-info cartTotal">
                Число блюд - <span data-select="cart__productsCount"><?=$orderModel->items_count ?></span> 
                Cумма - <span data-select="cart__totalPrice"><?=$orderModel->total_price ?></span> руб.
            </div>
        </div>
    </div>

    <div class="row" >
        <div class="col-md-12">
            <?= Html::beginForm(['cart', 'orderId' => $orderModel->id], 'post') ?>
                <?php foreach ($orderItems as $orderItem) : ?>
                    <?php
                        if (isset($products[$orderItem->product_id])) {
                            $product = $products[$orderItem->product_id];
                        } else {
                            $product = null;
                        }
                    ?>
                    <?= $this->render('_cartProduct', [
                        'orderItem' => $orderItem,
                        'product' => $product,
                    ]) ?>
                <?php endforeach; ?>
                <div>
                    <?= Html::submitButton('Дальше', ['name' => 'saveCartButton', 'class' => 'btn btn-success']) ?>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
    
</div>