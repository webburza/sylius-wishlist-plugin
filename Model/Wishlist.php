<?php

namespace Webburza\Sylius\WishlistBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Timestampable\Traits\Timestampable;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\User\Model\UserInterface;

class Wishlist implements WishlistInterface
{
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
     * @var boolean
     */
    protected $public = false;

    /**
     * @var ShopUserInterface
     */
    protected $user;

    /**
     * @var ArrayCollection|WishlistItemInterface[]
     */
    protected $items;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface
     */
    protected $updatedAt;

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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return WishlistInterface
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return Wishlist
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Wishlist
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * @param boolean $public
     *
     * @return WishlistInterface
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(?UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return ArrayCollection|WishlistItemInterface[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return bool
     */
    public function hasItems()
    {
        return !$this->items->isEmpty();
    }

    /**
     * @param WishlistItemInterface $item
     *
     * @return WishlistInterface
     */
    public function addItem(WishlistItemInterface $item)
    {
        $this->items->add($item);
        $item->setWishlist($this);

        return $this;
    }

    /**
     * @param ProductVariantInterface $productVariant
     *
     * @return bool
     */
    public function contains(ProductVariantInterface $productVariant)
    {
        foreach ($this->items as $wishlistItem) {
            if ($wishlistItem->getProductVariant() == $productVariant) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }
}
