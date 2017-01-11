<?php

namespace Webburza\Sylius\WishlistBundle\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;

interface WishlistFactoryInterface extends FactoryInterface
{
    /**
     * @param UserInterface $user
     *
     * @return WishlistInterface
     */
    public function createDefault(UserInterface $user);
}
