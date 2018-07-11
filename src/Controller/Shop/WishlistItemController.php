<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Controller\Shop;

use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Webburza\SyliusWishlistPlugin\Model\WishlistInterface;
use Webburza\SyliusWishlistPlugin\Model\WishlistItemInterface;
use Webburza\SyliusWishlistPlugin\Provider\LoggedInUserProviderInterface;
use Webburza\SyliusWishlistPlugin\Resolver\ProductVariantFromRequestResolverInterface;
use Webburza\SyliusWishlistPlugin\Resolver\WishlistFromRequestResolverInterface;

class WishlistItemController extends Controller
{
    /**
     * @var RepositoryInterface
     */
    protected $wishlistItemRepository;

    /**
     * @var LoggedInUserProviderInterface
     */
    protected $loggedInUserProvider;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var CartContextInterface
     */
    protected $cartContext;

    /**
     * @var ProductVariantRepositoryInterface
     */
    protected $productVariantRepository;

    /**
     * @var CartItemFactoryInterface
     */
    protected $cartItemFactory;

    /**
     * @var AddToCartCommandFactoryInterface
     */
    protected $addToCartCommandFactory;

    /**
     * @var WishlistFromRequestResolverInterface
     */
    protected $wishlistFromRequestResolver;

    /**
     * @var ProductVariantFromRequestResolverInterface
     */
    protected $productVariantFromRequestResolver;

    /**
     * @var FactoryInterface
     */
    protected $wishlistItemFactory;

    /**
     * @var bool
     */
    protected $multipleWishlistMode;

    /**
     * @param LoggedInUserProviderInterface $loggedInUserProvider
     * @param RepositoryInterface $wishlistItemRepository
     * @param TranslatorInterface $translator
     * @param CartContextInterface $cartContext
     * @param ProductVariantRepositoryInterface $productVariantRepository
     * @param CartItemFactoryInterface $cartItemFactory
     * @param AddToCartCommandFactoryInterface $addToCartCommandFactory
     * @param WishlistFromRequestResolverInterface $wishlistFromRequestResolver
     * @param ProductVariantFromRequestResolverInterface $productVariantFromRequestResolver
     * @param FactoryInterface $wishlistItemFactory
     * @param bool $multipleWishlistMode
     */
    public function __construct(
        LoggedInUserProviderInterface $loggedInUserProvider,
        RepositoryInterface $wishlistItemRepository,
        TranslatorInterface $translator,
        CartContextInterface $cartContext,
        ProductVariantRepositoryInterface $productVariantRepository,
        CartItemFactoryInterface $cartItemFactory,
        AddToCartCommandFactoryInterface $addToCartCommandFactory,
        WishlistFromRequestResolverInterface $wishlistFromRequestResolver,
        ProductVariantFromRequestResolverInterface $productVariantFromRequestResolver,
        FactoryInterface $wishlistItemFactory,
        bool $multipleWishlistMode
    ) {
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->wishlistItemRepository = $wishlistItemRepository;
        $this->translator = $translator;
        $this->cartContext = $cartContext;
        $this->productVariantRepository = $productVariantRepository;
        $this->cartItemFactory = $cartItemFactory;
        $this->addToCartCommandFactory = $addToCartCommandFactory;
        $this->wishlistFromRequestResolver = $wishlistFromRequestResolver;
        $this->productVariantFromRequestResolver = $productVariantFromRequestResolver;
        $this->wishlistItemFactory = $wishlistItemFactory;
        $this->multipleWishlistMode = $multipleWishlistMode;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request) : Response
    {
        // Get (or create) the wishlist to which the item should be added
        $wishlist = $this->wishlistFromRequestResolver->resolve($request);

        // Get the product variant to be added to wishlist
        $productVariant = $this->productVariantFromRequestResolver->resolve($request);

        // Prevent duplicates
        if ($wishlist->containsVariant($productVariant)) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(null, Response::HTTP_CONFLICT);
            }

            // Set flash message
            $this->addFlash(
                'info',
                $this->translator->trans('webburza_sylius_wishlist.flash.already_on_wishlist')
            );

            // Redirect back to the wishlist
            return $this->redirectToWishlist($wishlist);
        }

        /** @var WishlistItemInterface $wishlistItem */
        $wishlistItem = $this->wishlistItemFactory->createNew();
        $wishlistItem->setProductVariant($productVariant);

        $wishlist->addItem($wishlistItem);

        // Persist the wishlist item
        $this->wishlistItemRepository->add($wishlistItem);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_CREATED);
        }

        // Set success message
        $this->addFlash(
            'success', $this->translator->trans('webburza_sylius_wishlist.flash.item_added')
        );

        // Redirect back to the wishlist
        return $this->redirectToWishlist($wishlist);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function addToCartAction(Request $request) : Response
    {
        // Get product variant
        $variant = $this->productVariantRepository->find($request->get('variantId'));

        // Check if product variant found
        if (!$variant) {
            throw $this->createNotFoundException();
        }

        // Create an add-to-cart command
        $addToCartCommand = $this->addToCartCommandFactory->createWithCartAndCartItem(
            $this->cartContext->getCart(),
            $this->cartItemFactory->createForProduct($variant->getProduct())
        );

        // Prepare the form to be rendered for the add-to-cart command
        $form = $this->get('form.factory')->create(AddToCartType::class, $addToCartCommand, [
            'product' => $variant->getProduct()
        ]);

        $form->get('cartItem')->get('quantity')->setData(1);

        if ($form->get('cartItem')->has('variant')) {
            $form->get('cartItem')->get('variant')->setData($variant);
        }

        // Render the view
        return $this->render('@WebburzaSyliusWishlistPlugin/Resources/views/Shop/Wishlist/_cartForm.html.twig', [
            'product' => $variant->getProduct(),
            'form'    => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function removeAction(Request $request) : Response
    {
        /** @var WishlistItemInterface $wishlistItem */
        $wishlistItem = $this->wishlistItemRepository->find($request->get('id'));

        // Check if wishlist item found
        if (!$wishlistItem) {
            throw $this->createNotFoundException();
        }

        // Check if this item belongs to the current customer trying to remove it
        if ($wishlistItem->getWishlist()->getUser() != $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Remove the item from the repository
        $this->wishlistItemRepository->remove($wishlistItem);

        // If this was an AJAX request, return appropriate response
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        // Set success message
        $this->addFlash(
            'success', $this->translator->trans('webburza_sylius_wishlist.flash.item_removed')
        );

        return $this->redirectToWishlist($wishlistItem->getWishlist());
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
}
