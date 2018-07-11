<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class Wishlist implements WishlistInterface
{
    use TimestampableTrait;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $public = false;

    /**
     * @var ShopUserInterface
     */
    protected $user;

    /**
     * @var Collection|WishlistItemInterface[]
     */
    protected $items;

    /**
     * Wishlist constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle() : ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return WishlistInterface
     */
    public function setTitle(?string $title) : WishlistInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug() : string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return WishlistInterface
     */
    public function setSlug(string $slug) : WishlistInterface
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return WishlistInterface
     */
    public function setDescription(?string $description) : WishlistInterface
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublic() : bool
    {
        return $this->public;
    }

    /**
     * @param bool $public
     *
     * @return WishlistInterface
     */
    public function setPublic(bool $public) : WishlistInterface
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return ShopUserInterface
     */
    public function getUser() : ShopUserInterface
    {
        return $this->user;
    }

    /**
     * @param ShopUserInterface $user
     *
     * @return WishlistInterface
     */
    public function setUser(ShopUserInterface $user) : WishlistInterface
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|WishlistItemInterface[]
     */
    public function getItems() : Collection
    {
        return $this->items;
    }

    /**
     * @return bool
     */
    public function hasItems() : bool
    {
        return !$this->items->isEmpty();
    }

    /**
     * @param WishlistItemInterface $item
     *
     * @return WishlistInterface
     */
    public function addItem(WishlistItemInterface $item) : WishlistInterface
    {
        $this->items->add($item);
        $item->setWishlist($this);

        return $this;
    }

    /**
     * @param WishlistItemInterface $item
     *
     * @return WishlistInterface
     */
    public function removeItem(WishlistItemInterface $item) : WishlistInterface
    {
        $this->items->removeElement($item);

        return $this;
    }

    /**
     * @return WishlistInterface
     */
    public function clearItems() : WishlistInterface
    {
        foreach ($this->getItems() as $item) {
            $this->removeItem($item);
        }

        return $this;
    }

    /**
     * @param ProductVariantInterface $productVariant
     *
     * @return bool
     */
    public function containsVariant(ProductVariantInterface $productVariant) : bool
    {
        foreach ($this->items as $wishlistItem) {
            if ($wishlistItem->getProductVariant() === $productVariant) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function containsProduct(ProductInterface $product) : bool
    {
        foreach ($this->items as $wishlistItem) {
            if ($wishlistItem->getProductVariant()->getProduct() === $product) {
                return true;
            }
        }

        return false;
    }
}
