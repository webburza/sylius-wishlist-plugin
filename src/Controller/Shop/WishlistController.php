<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Controller\Shop;

use FOS\RestBundle\View\ConfigurableViewHandlerInterface;
use FOS\RestBundle\View\View;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Webburza\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use Webburza\SyliusWishlistPlugin\Model\WishlistInterface;
use Webburza\SyliusWishlistPlugin\Provider\LoggedInUserProviderInterface;
use Webburza\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;

class WishlistController extends Controller
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
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var bool
     */
    protected $multipleWishlistMode;

    /**
     * @var ConfigurableViewHandlerInterface
     */
    protected $restViewHandler;

    /**
     * @param LoggedInUserProviderInterface $loggedInUserProvider
     * @param WishlistRepositoryInterface $wishlistRepository
     * @param WishlistFactoryInterface $wishlistFactory
     * @param TranslatorInterface $translator
     * @param ConfigurableViewHandlerInterface $restViewHandler
     * @param bool $multipleWishlistMode
     */
    public function __construct(
        LoggedInUserProviderInterface $loggedInUserProvider,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        TranslatorInterface $translator,
        ConfigurableViewHandlerInterface $restViewHandler,
        bool $multipleWishlistMode
    ) {
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistFactory = $wishlistFactory;
        $this->translator = $translator;
        $this->restViewHandler = $restViewHandler;
        $this->multipleWishlistMode = $multipleWishlistMode;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function firstAction(Request $request) : Response
    {
        // Abort if no logged-in user
        if (!($loggedInUser = $this->loggedInUserProvider->getUser())) {
            throw $this->createNotFoundException();
        }

        // Get the first wishlist for the user
        $wishlist = $this->wishlistRepository->getFirstForUser($loggedInUser);

        // Create a wishlist if none exists
        if (!$wishlist) {
            $wishlist = $this->wishlistFactory->createDefault($loggedInUser);
            $this->wishlistRepository->add($wishlist);
        }

        // If the bundle is configured to work with multiple wishlists
        // Redirect to the current wishlist to update URI
        if ($this->multipleWishlistMode) {
            return $this->redirectToRoute('webburza_sylius_wishlist_shop_wishlist_show', [
                'id' => $wishlist->getId(),
                'slug' => $wishlist->getSlug()
            ]);
        }

        return $this->handleView(
            '@WebburzaSyliusWishlistPlugin/Resources/views/Shop/Wishlist/show.html.twig', [
                'wishlist' => $wishlist
            ], $request
        );
    }

    /**
     * @param Request $request
     *
     * @return Response $response
     */
    public function clearAction(Request $request) : Response
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findOneBy([
            'id' => $request->get('id'),
            'user' => $this->getUser()
        ]);

        // Check if wishlist found
        if (!$wishlist) {
            throw $this->createNotFoundException();
        }

        // Clear items from wishlist
        $wishlist->clearItems();

        // Persist changes
        $this->wishlistRepository->add($wishlist);

        // If this was an AJAX request, return appropriate response
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(null, 200);
        }

        // Set success message
        $this->addFlash(
            'success', $this->translator->trans('webburza_sylius_wishlist.flash.cleared')
        );

        return $this->redirectToWishlist($wishlist);
    }

    /**
     * Get a publicly visible wishlist by id.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request): Response
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findOneBy([
            'id' => $request->get('id'),
            'slug' => $request->get('slug')
        ]);

        // Check if wishlist exists, and it can be accessed
        if (!($wishlist && $this->userCanAccessWishlist(
            $this->loggedInUserProvider->getUser(), $wishlist)
        )) {
            throw $this->createNotFoundException();
        }

        return $this->handleView(
            '@WebburzaSyliusWishlistPlugin/Resources/views/Shop/Wishlist/show.html.twig', [
                'wishlist' => $wishlist
            ], $request
        );
    }

    /**
     * Check if a wishlist is publicly available, or the
     * user has special privileges to access it.
     *
     * @param ShopUserInterface $user
     * @param WishlistInterface $wishlist
     *
     * @return bool
     */
    protected function userCanAccessWishlist(
        ShopUserInterface $user = null,
        WishlistInterface $wishlist
    ) : bool {
        return $wishlist->isPublic() || ($user && $user->getId() === $wishlist->getUser()->getId());
    }

    /**
     * @param WishlistInterface $wishlist
     *
     * @return RedirectResponse
     */
    protected function redirectToWishlist(WishlistInterface $wishlist) : RedirectResponse
    {
        // If the bundle is configured to work with a single wishlist,
        // Redirect to the the general route (without an id)
        if (false == $this->multipleWishlistMode) {
            return $this->redirectToRoute('webburza_sylius_wishlist_shop_wishlist_first');
        }

        // Redirect back to the same wishlist
        return $this->redirectToRoute('webburza_sylius_wishlist_shop_wishlist_show', [
            'id' => $wishlist->getId(),
            'slug' => $wishlist->getSlug()
        ]);
    }

    /**
     * @param string $template
     * @param array $data
     * @param Request $request
     *
     * @return Response
     */
    protected function handleView(string $template, array $data = [], Request $request) : Response
    {
        // Consider first item in $data as main resource
        $resource = !empty($data) ? array_values($data)[0] : null;

        // Create the view
        $view = View::create($resource);

        // Set template if HTML request, or serialization data and format if Ajax
        if (!$request->isXmlHttpRequest()) {
            $view->setTemplate($template)
                 ->setData($data);
        } else {
            $view->getContext()->enableMaxDepth();
            $view->setFormat('json');
        }

        // Handle the view and return response
        return $this->restViewHandler->handle($view);
    }
}
