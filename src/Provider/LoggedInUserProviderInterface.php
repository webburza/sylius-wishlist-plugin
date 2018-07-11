<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Provider;

use Sylius\Component\Core\Model\ShopUserInterface;

interface LoggedInUserProviderInterface
{
    /**
     * @return ShopUserInterface|null
     */
    public function getUser() : ?ShopUserInterface;
}
