<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use app\modules\orders\assets\MenuAsset;
use app\modules\orders\assets\OrderCssAsset;

/* @var $this yii\web\View */

$this->title = 'Меню';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $orderModel->id, 'url' => ['view', 'orderId' => $orderModel->id]];
$this->params['breadcrumbs'][] = $this->title;

MenuAsset::register($this);
OrderCssAsset::register($this);

$addToCartUrl = Url::to(['order/add-to-cart', 'orderId' => $orderModel->id]);

$params = <<<JS
   {
       'addToCartUrl' : '$addToCartUrl'
   }    
JS;

$this->registerJs("MenuScript.init($params);", View::POS_READY);
?>
<div class="orders-order-menu">

    <?= $this->render('_orderMenu', ['orderModel' => $orderModel]) ?>
    
    <div class="row topPanel">
        <div class="col-md-2"> 
        <?php Modal::begin([
            'header' => '<h2>Выберите город</h2>',
            'toggleButton' => [
                'label' => $cityName,
                'tag' => 'span',
                'class' => 'citySelectButton text-info'
            ],
        ]); ?>
            <?php foreach ($citiesList as $id => $name) : ?>
                <p>
                    <?= Html::a($name, ['menu', 'orderId' => $orderModel->id, 'cityId' => $id], ['class' => 'text-primary cityName']) ?>
                </p>
            <?php endforeach; ?>
        <?php Modal::end(); ?>
        </div>
        <div class="col-md-4">
            <?=Html::beginForm(['menu', 'orderId' => $orderModel->id], 'get') ?>
                <div class="form-group">
                    <input type="text" name="search" value="" id="searchInput" class="form-control" />
                </div>
            <?=Html::endForm() ?>
        </div>
        <div class="col-md-4">
            <div class="bg-info cartTotal">
                Число блюд - <span data-select="cart__productsCount"><?=$orderModel->items_count ?></span> 
                Cумма - <span data-select="cart__totalPrice"><?= $orderModel->total_price?></span> руб.
            </div>
        </div>
        <div class="col-md-2 text-right">
            <?= Html::a('Дальше', ['cart', 'orderId' => $orderModel->id], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <p>
                <?= Html::a('Все', ['menu', 'orderId' => $orderModel->id], ['class' => 'btn btn-primary']) ?> 
            </p>
            <?php foreach ($categories as $category) : ?>
                <p>
                    <?= Html::a($category->name, ['menu', 'orderId' => $orderModel->id, 'categoryId' => $category->id], ['class' => 'btn btn-primary']) ?> 
                </p>
            <?php endforeach; ?>
        </div>
        <div class="col-md-10">
            <?php if ($renderType === 'category') : ?>
                <?php $category = $categories[$categoryId] ?>
                <?= $this->render('_category', [
                    'category' => $category,
                    'products' => $products,
                ]); ?>
            <?php elseif ($renderType === 'search') : ?>
                <?php foreach ($products as $product) : ?>
                    <?= $this->render('_product', ['product' => $product]) ?>
                <?php endforeach; ?>
            <?php elseif ($renderType === 'all') : ?>
                 <?php foreach ($categories as $category) : ?>
                    <?= $this->render('_category', [
                        'category' => $category,
                        'products' => $products,
                    ]); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
</div>

