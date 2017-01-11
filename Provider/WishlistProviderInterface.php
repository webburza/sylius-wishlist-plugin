<?php

namespace Webburza\Sylius\WishlistBundle\Provider;

use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;

interface WishlistProviderInterface
{
    /**
     * @return WishlistInterface[]
     */
    public function getWishlists();

    /**
     * @return int
     */
    public function getItemCount();
}
