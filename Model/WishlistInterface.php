<?php

namespace Webburza\Sylius\WishlistBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\User\Model\UserAwareInterface;

/**
 * WishlistInterface
 */
interface WishlistInterface extends ResourceInterface, UserAwareInterface, TimestampableInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return WishlistInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @param string $slug
     *
     * @return WishlistInterface
     */
    public function setSlug($slug);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     *
     * @return WishlistInterface
     */
    public function setDescription($description);

    /**
     * @return boolean
     */
    public function isPublic();

    /**
     * @param boolean $public
     *
     * @return WishlistInterface
     */
    public function setPublic($public);

    /**
     * @return ArrayCollection|WishlistItemInterface[]
     */
    public function getItems();

    /**
     * @return bool
     */
    public function hasItems();

    /**
     * @param WishlistItemInterface $item
     *
     * @return WishlistInterface
     */
    public function addItem(WishlistItemInterface $item);

    /**
     * @param ProductVariantInterface $productVariant
     *
     * @return bool
     */
    public function contains(ProductVariantInterface $productVariant);
}
