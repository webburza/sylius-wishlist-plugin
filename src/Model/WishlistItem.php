<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Model;

use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class WishlistItem implements WishlistItemInterface
{
    use TimestampableTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var WishlistInterface
     */
    protected $wishlist;

    /**
     * @var ProductVariantInterface
     */
    protected $productVariant;

    /**
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @return WishlistInterface
     */
    public function getWishlist() : WishlistInterface
    {
        return $this->wishlist;
    }

    /**
     * @param WishlistInterface $wishlist
     *
     * @return WishlistItemInterface
     */
    public function setWishlist(WishlistInterface $wishlist) : WishlistItemInterface
    {
        $this->wishlist = $wishlist;

        return $this;
    }

    /**
     * @return ProductVariantInterface
     */
    public function getProductVariant() : ProductVariantInterface
    {
        return $this->productVariant;
    }

    /**
     * @param ProductVariantInterface $productVariant
     *
     * @return WishlistItemInterface
     */
    public function setProductVariant(ProductVariantInterface $productVariant) : WishlistItemInterface
    {
        $this->productVariant = $productVariant;

        return $this;
    }
}
