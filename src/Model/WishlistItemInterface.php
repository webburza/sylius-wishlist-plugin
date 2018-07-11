<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Model;

use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface WishlistItemInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @return WishlistInterface
     */
    public function getWishlist() : WishlistInterface;

    /**
     * @param WishlistInterface $wishlist
     *
     * @return self
     */
    public function setWishlist(WishlistInterface $wishlist) : self;

    /**
     * @return ProductVariantInterface
     */
    public function getProductVariant() : ProductVariantInterface;

    /**
     * @param ProductVariantInterface $productVariant
     *
     * @return self
     */
    public function setProductVariant(ProductVariantInterface $productVariant) : self;
}
