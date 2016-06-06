<?php

namespace Webburza\Sylius\WishlistBundle\Model;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Security\Core\SecurityContext;

class WishlistProvider
{
    /**
     * @var WishlistRepositoryInterface
     */
    protected $wishlistRepository;

    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @var WishlistInterface[]
     */
    protected $wishlists;

    /**
     * WishlistProvider constructor.
     *
     * @param SecurityContext $securityContext
     * @param WishlistRepositoryInterface $wishlistRepository
     */
    public function __construct(
        SecurityContext $securityContext,
        WishlistRepositoryInterface $wishlistRepository
    ) {
        $this->securityContext = $securityContext;
        $this->wishlistRepository = $wishlistRepository;
    }

    /**
     * @return string
     */
    public function getWishlists()
    {
        if (null === $this->wishlists) {
            // Get customer
            $customer = $this->getCustomer();

            if ($customer) {
                $this->wishlists = $this->wishlistRepository->getWishlistsForCustomer($customer);
            }
        }

        return $this->wishlists;
    }

    /**
     * @return CustomerInterface null
     */
    protected function getCustomer()
    {
        $customer = null;

        if ($user = $this->securityContext->getToken()->getUser()) {
            $customer = $user->getCustomer();
        }

        return $customer;
    }
}
