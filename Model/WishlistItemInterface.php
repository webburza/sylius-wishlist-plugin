<?php

namespace Webburza\Sylius\WishlistBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Product\Model\VariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Webburza\Sylius\WishlistBundle\Entity\WishlistItem;

/**
 * WishlistItemInterface
 */
interface WishlistItemInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return WishlistInterface
     */
    public function getWishlist();

    /**
     * @param WishlistInterface $wishlist
     * @return WishlistItem
     */
    public function setWishlist(WishlistInterface $wishlist);

    /**
     * @return VariantInterface
     */
    public function getProductVariant();

    /**
     * @param VariantInterface $productVariant
     * @return WishlistItem
     */
    public function setProductVariant(VariantInterface $productVariant);
}
