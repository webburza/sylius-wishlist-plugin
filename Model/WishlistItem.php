<?php

namespace Webburza\Sylius\WishlistBundle\Model;

use Gedmo\Timestampable\Traits\Timestampable;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;
use Webburza\Sylius\WishlistBundle\Model\WishlistItemInterface;

class WishlistItem implements WishlistItemInterface
{
    use Timestampable;

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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return WishlistInterface
     */
    public function getWishlist()
    {
        return $this->wishlist;
    }

    /**
     * @param WishlistInterface $wishlist
     *
     * @return WishlistItemInterface
     */
    public function setWishlist(WishlistInterface $wishlist)
    {
        $this->wishlist = $wishlist;

        return $this;
    }

    /**
     * @return ProductVariantInterface
     */
    public function getProductVariant()
    {
        return $this->productVariant;
    }

    /**
     * @param ProductVariantInterface $productVariant
     *
     * @return WishlistItemInterface
     */
    public function setProductVariant(ProductVariantInterface $productVariant)
    {
        $this->productVariant = $productVariant;

        return $this;
    }
}
