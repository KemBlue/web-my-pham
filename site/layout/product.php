<div class="product-container">
    <div class="image">
        <img class="img-responsive" src="../upload/<?=$product->getFeaturedImage()?>" alt="">
    </div>
    <div class="product-meta">
        <h5 class="name">
            <?php 
                $productName = $product->getName();
                $slug = $slugify->slugify($productName);
                $link =  $router->generate('productDetail',['slug' => $slug, 'id' => $product->getId()]);
            ?>
            <a class="product-name" href="<?=$link?>" title="<?=$product->getName()?>"><?=$product->getName()?></a>
        </h5>
        <div class="product-item-price">
            <?php if($product->getPrice() != $product->getSalePrice()): ?>
            <span class="product-item-regular"><?=formatMoney($product->getPrice())?></span>
            <?php endif ?>
            <span class="product-item-discount"><?=formatMoney($product->getSalePrice())?></span>
        </div>
    </div>
    <div class="button-product-action clearfix">
        <?php if($product->getInventoryQty()>0): ?>
        <div class="cart icon">
            <a class="btn btn-outline-inverse buy" product-id="<?=$product->getId()?>" href="javascript:void(0)"
                title="Thêm vào giỏ">
                Thêm vào giỏ <i class="fa fa-shopping-cart"></i>
            </a>
        </div>
        <?php endif ?>
        <div class="quickview icon">
            <a class="btn btn-outline-inverse" href="<?=$link?>" title="Xem nhanh">
                Xem chi tiết <i class="fa fa-eye"></i>
            </a>
        </div>
    </div>
</div>