<?php

namespace Webburza\Sylius\WishlistBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\User\Model\CustomerAwareInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * WishlistInterface
 */
interface WishlistInterface extends ResourceInterface, CustomerAwareInterface, TimestampableInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return WishlistInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @param string $slug
     * @return WishlistInterface
     */
    public function setSlug($slug);

    /**
     * @return boolean
     */
    public function isPublic();

    /**
     * @param boolean $public
     * @return WishlistInterface
     */
    public function setPublic($public);

    /**
     * @return WishlistItemInterface[]
     */
    public function getItems();

    /**
     * @param ArrayCollection $items
     * @return WishlistInterface
     */
    public function setItems(ArrayCollection $items);

    /**
     * @param WishlistItemInterface $item
     * @return WishlistInterface
     */
    public function addItem(WishlistItemInterface $item);
}
