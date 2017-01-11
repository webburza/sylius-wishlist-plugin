<?php

namespace Webburza\Sylius\WishlistBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Symfony\Component\Validator\Constraints as Assert;

interface WishlistItemInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @return WishlistInterface
     */
    public function getWishlist();

    /**
     * @param WishlistInterface $wishlist
     *
     * @return WishlistItemInterface
     */
    public function setWishlist(WishlistInterface $wishlist);

    /**
     * @return ProductVariantInterface
     */
    public function getProductVariant();

    /**
     * @param ProductVariantInterface $productVariant
     *
     * @return WishlistItemInterface
     */
    public function setProductVariant(ProductVariantInterface $productVariant);
}
