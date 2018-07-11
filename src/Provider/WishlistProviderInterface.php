<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Provider;

use Webburza\SyliusWishlistPlugin\Model\WishlistInterface;

interface WishlistProviderInterface
{
    /**
     * @return WishlistInterface[]
     */
    public function getWishlists() : array;

    /**
     * @return int
     */
    public function getItemCount() : int;
}
