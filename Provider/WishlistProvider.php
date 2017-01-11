<?php

namespace Webburza\Sylius\WishlistBundle\Provider;

use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;
use Webburza\Sylius\WishlistBundle\Repository\WishlistRepositoryInterface;

class WishlistProvider implements WishlistProviderInterface
{
    /**
     * @var WishlistRepositoryInterface
     */
    protected $repository;

    /**
     * @var WishlistInterface[]
     */
    protected $wishlists;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @param WishlistRepositoryInterface $repository
     * @param TokenStorage $tokenStorage
     */
    public function __construct(
        WishlistRepositoryInterface $repository,
        TokenStorage $tokenStorage
    ) {
        $this->repository = $repository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return WishlistInterface[]
     */
    public function getWishlists()
    {
        if (null === $this->wishlists) {
            if ($user = $this->getUser()) {
                $this->wishlists = $this->repository->findBy(['user' => $user]);
            }
        }

        return $this->wishlists;
    }

    /**
     * @return int
     */
    public function getItemCount()
    {
        $itemCount = 0;

        if ($this->getWishlists()) {
            foreach ($this->getWishlists() as $wishlist) {
                $itemCount += $wishlist->getItems()->count();
            }
        }

        return $itemCount;
    }

    /**
     * @return UserInterface
     */
    protected function getUser()
    {
        if ($securityToken = $this->tokenStorage->getToken()) {
            if (($user = $securityToken->getUser()) instanceof UserInterface) {
                return $user;
            }
        }

        return null;
    }
}
