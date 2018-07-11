<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Resolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webburza\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use Webburza\SyliusWishlistPlugin\Model\WishlistInterface;
use Webburza\SyliusWishlistPlugin\Provider\LoggedInUserProviderInterface;
use Webburza\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;

class WishlistFromRequestResolver implements WishlistFromRequestResolverInterface
{
    /**
     * @var LoggedInUserProviderInterface
     */
    protected $loggedInUserProvider;

    /**
     * @var WishlistRepositoryInterface
     */
    protected $wishlistRepository;

    /**
     * @var WishlistFactoryInterface
     */
    protected $wishlistFactory;

    /**
     * @param LoggedInUserProviderInterface $loggedInUserProvider
     * @param WishlistRepositoryInterface $wishlistRepository
     * @param WishlistFactoryInterface $wishlistFactory
     */
    public function __construct(
        LoggedInUserProviderInterface $loggedInUserProvider,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory
    ) {
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistFactory = $wishlistFactory;
    }

    /**
     * Get the requested wishlist, if any, or the first one for the logged-in user.
     * If none exists for the user, create a new one.
     *
     * @param Request $request
     *
     * @return WishlistInterface
     */
    public function resolve(Request $request) : WishlistInterface
    {
        // Abort if no logged-in user
        if (!($user = $this->loggedInUserProvider->getUser())) {
            throw new BadRequestHttpException();
        }

        // Check if a specific wishlist was requested
        if ($wishlistId = $request->get('wishlistId')) {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistRepository->findOneBy([
                'id'   => $wishlistId,
                'user' => $user
            ]);

            if (!$wishlist) {
                throw new NotFoundHttpException();
            }

            return $wishlist;
        }

        // If not, get the first wishlist for the user
        $wishlist = $this->wishlistRepository->getFirstForUser($user);

        // If no wishlist found, create a new one
        if (!$wishlist) {
            $wishlist = $this->wishlistFactory->createDefault($user);
            $this->wishlistRepository->add($wishlist);
        }

        return $wishlist;
    }
}
