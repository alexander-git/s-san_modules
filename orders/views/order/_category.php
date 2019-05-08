<?php if (isset($products[$category->id])) : ?>
    <div class="categoryName bg-info">
        <?= $category->name; ?>
    </div>
    <br />
    <?php $categoryProducts = $products[$category->id]; ?> 
        <?php foreach ($categoryProducts as $product) : ?>
        <?= $this->render('_product', ['product' => $product]) ?>
    <?php endforeach;?>
    <br style="clear : both;" />
<?php endif; ?>