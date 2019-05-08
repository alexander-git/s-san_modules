<?php

use yii\helpers\Html;

$dataAttributesArr = [];
$dataAttributesArr['data-product-id'] = $product->id;
$dataAttributesArr['data-has-options'] = (int) $product->hasOptions;
if ($product->hasOptions) {
    foreach ($product->prices as $key => $value) {
        $dataAttributesArr['data-price-'.$key] = $value;
    }
    foreach ($product->optionIds as $key => $value) {
        $dataAttributesArr['data-option-id-'.$key] = $value;
    }
}
$dataAttributesStr = '';
foreach ($dataAttributesArr as $key=> $value) {
    $dataAttributesStr .= $key.'="'.$value.'" ';
}

?>
<div class="product" data-select="product__product" <?=$dataAttributesStr ?> >
    <div class="clearfix">
        <div class="product__productName pull-left">
            <?= $product->name ?>
        </div>
        <div class="product__price pull-right">
            <div data-select="product__price"><?= $product->initialPrice ?></div>
        </div>
    </div>
    <div class="clearfix">
        <div class="pull-left product__imageContainer">
            <?php if (!empty($product->imageUrl)) : ?>
                <?= Html::img($product->imageUrl, ['class' => 'product__image']) ?>
            <?php endif; ?>
        </div>
        <div class="pull-left">
     
            <?php if (!empty($product->optionsData)) : ?>
            <div data-select="product__optionsContainer">
                <?php foreach ($product->optionsData as $item) : ?>
                    <div class="product__optionName">
                        <?= $item->propertyName ?>
                    </div>
                    <div class="product__optionValues">
                        <?php
                            // autocomplete' => 'off' нужно обязательно, так 
                            // начальная цена устанавливается той, которая 
                            // есть у первой комбинации. Иначе при запоминании
                            // ранее выбранных флажков цена не будет им 
                            // соответсвовать.
                        ?>
                        <?= Html::radioList($product->id.'-'.$item->propertyId, $item->selected, $item->values, [
                            'itemOptions' => [
                                'data-select' => 'product__option',
                                'autocomplete' => 'off',
                            ],
                            'separator' => '<br />',
                        ]); ?>
                    </div>
                    <br />
                <?php endforeach; ?>       
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="clearfix">
        <div class="pull-right product__addButtonContainer">
            <?= Html::button('Добавить', ['class' => 'btn btn-success', 'data-select' => 'product__addButton']) ?>
        </div>
    </div>
</div>