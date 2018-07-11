<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface WishlistInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @return string
     */
    public function getTitle() : ?string;

    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle(?string $title) : self;

    /**
     * @return string
     */
    public function getSlug() : string;

    /**
     * @param string $slug
     *
     * @return self
     */
    public function setSlug(string $slug) : self;

    /**
     * @return string
     */
    public function getDescription() : ?string;

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription(?string $description) : self;

    /**
     * @return bool
     */
    public function isPublic() : bool;

    /**
     * @param bool $public
     *
     * @return self
     */
    public function setPublic(bool $public) : self;

    /**
     * @return ShopUserInterface
     */
    public function getUser() : ShopUserInterface;

    /**
     * @param ShopUserInterface $user
     *
     * @return self
     */
    public function setUser(ShopUserInterface $user) : self;

    /**
     * @return Collection|WishlistItemInterface[]
     */
    public function getItems() : Collection;

    /**
     * @return bool
     */
    public function hasItems() : bool;

    /**
     * @param WishlistItemInterface $item
     *
     * @return self
     */
    public function addItem(WishlistItemInterface $item) : self;

    /**
     * @param WishlistItemInterface $item
     *
     * @return self
     */
    public function removeItem(WishlistItemInterface $item) : self;

    /**
     * @return self
     */
    public function clearItems() : self;

    /**
     * @param ProductVariantInterface $productVariant
     *
     * @return bool
     */
    public function containsVariant(ProductVariantInterface $productVariant) : bool;

    /**
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function containsProduct(ProductInterface $product) : bool;
}
