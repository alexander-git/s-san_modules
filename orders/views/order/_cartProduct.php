<?php

use yii\helpers\Html;
use kartik\touchspin\TouchSpin;

$touchspinPluginOptions = [
    'step' => 1,
    'min' => 1,
    'max' => 100000000,
    'verticalbuttons' => true,
    'verticalupclass' => 'glyphicon glyphicon-plus',
    'verticaldownclass' => 'glyphicon glyphicon-minus',
];

?>
<div class="cartProduct" data-select="cartProduct__product">
    <div class="cartProduct__productName">
         <?= $product->name ?>
    </div>
    <div class="cartProduct__imageContainer">
        <?php if (!empty($product->imageUrl)) : ?>
            <?= Html::img($product->imageUrl, ['class' => 'cartProduct__image']) ?>
        <?php endif; ?>
    </div>
    <div class="cartProduct__count">
        <?= TouchSpin::widget([
            'name' => "productCounts[$product->id]",
            'value' => $orderItem->quantity,
            'options' => [
                'data-select' => 'cartProduct__countInput',
                'class' => 'form-control'
            ],
            'pluginOptions' => $touchspinPluginOptions,
        ]); ?>
    </div>
    <div class="cartProduct__price">
        Цена - <span data-select="cartProduct__price"><?= $orderItem->price ?></span>
    </div>
    <div class="cartProduct__totalPrice">
        Сумма - <span data-select="cartProduct__totalPrice"><?= ($orderItem->total_price) ?></span>
    </div>
    <div class="cartProduct__deleteButton">
        <span class="btn btn-primary glyphicon glyphicon-trash" data-select="cartProduct__deleteButton"></span>
    </div>
</div>
