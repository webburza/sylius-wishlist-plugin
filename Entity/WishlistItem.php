<?php

namespace Webburza\Sylius\WishlistBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Product\Model\VariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;
use Webburza\Sylius\WishlistBundle\Model\WishlistItemInterface;

/**
 * Wishlist
 *
 * @ORM\Table(name="webburza_sylius_wishlist_item")
 * @ORM\Entity()
 */
class WishlistItem implements ResourceInterface, WishlistItemInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var WishlistInterface
     * @ORM\ManyToOne(targetEntity="\Webburza\Sylius\WishlistBundle\Model\WishlistInterface", inversedBy="items")
     * @ORM\JoinColumn(name="wishlist_id", nullable=false, onDelete="cascade")
     */
    private $wishlist;

    /**
     * @var VariantInterface
     * @ORM\ManyToOne(targetEntity="\Sylius\Component\Product\Model\VariantInterface")
     * @ORM\JoinColumn(name="product_variant_id", nullable=false, onDelete="cascade")
     */
    private $productVariant;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

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
     * @return WishlistItem
     */
    public function setWishlist(WishlistInterface $wishlist)
    {
        $this->wishlist = $wishlist;

        return $this;
    }

    /**
     * @return VariantInterface
     */
    public function getProductVariant()
    {
        return $this->productVariant;
    }

    /**
     * @param VariantInterface $productVariant
     * @return WishlistItem
     */
    public function setProductVariant(VariantInterface $productVariant)
    {
        $this->productVariant = $productVariant;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return WishlistItem
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return WishlistItem
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
