<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Provider;

use Webburza\SyliusWishlistPlugin\Model\WishlistInterface;
use Webburza\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;

class WishlistProvider implements WishlistProviderInterface
{
    /**
     * @var WishlistRepositoryInterface
     */
    protected $repository;

    /**
     * @var LoggedInUserProviderInterface
     */
    protected $loggedInUserProvider;

    /**
     * @var WishlistInterface[]
     */
    protected $wishlists;

    /**
     * @param WishlistRepositoryInterface $repository
     * @param LoggedInUserProviderInterface $loggedInUserProvider
     */
    public function __construct(
        WishlistRepositoryInterface $repository,
        LoggedInUserProviderInterface $loggedInUserProvider
    ) {
        $this->repository = $repository;
        $this->loggedInUserProvider = $loggedInUserProvider;
    }

    /**
     * @return WishlistInterface[]
     */
    public function getWishlists() : array
    {
        if (null === $this->wishlists) {
            if ($user = $this->loggedInUserProvider->getUser()) {
                $this->wishlists = $this->repository->findBy(['user' => $user]);
            }
        }

        return $this->wishlists ?: [];
    }

    /**
     * @return int
     */
    public function getItemCount() : int
    {
        $itemCount = 0;

        if ($this->getWishlists()) {
            foreach ($this->getWishlists() as $wishlist) {
                $itemCount += $wishlist->getItems()->count();
            }
        }

        return $itemCount;
    }
}
