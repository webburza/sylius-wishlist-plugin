<?php

namespace Webburza\Sylius\WishlistBundle\Controller\Frontend;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;
use Webburza\Sylius\WishlistBundle\Model\WishlistItemInterface;

class WishlistItemController extends FOSRestController
{
    /**
     * WishlistItemController constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * This action renders a partial template to be submitted to
     * the default add-to-cart endpoint, as used on product page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addToCartAction(Request $request)
    {
        $cart = $this->get('sylius.context.cart')->getCart();

        $variant =
            $this->get('sylius.repository.product_variant')->find($request->get('variantId'));

        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->get('sylius.factory.order_item')
                          ->createForProduct($variant->getProduct());

        $addToCartCommand =
            $this->get('sylius.factory.add_to_cart_command')
                 ->createWithCartAndCartItem($cart, $orderItem);

        $form = $this->get('form.factory')->create(AddToCartType::class, $addToCartCommand, [
            'product' => $variant->getProduct()
        ]);

        $form->get('cartItem')->get('quantity')->setData(1);

        if ($form->get('cartItem')->has('variant')) {
            $form->get('cartItem')->get('variant')->setData($variant);
        }

        $view = View::create([
            'product' => $variant->getProduct(),
            'form'    => $form->createView()
        ]);

        $view->setTemplate('@WebburzaSyliusWishlist/Frontend/Wishlist/_cartForm.html.twig');

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function removeAction(Request $request)
    {
        /** @var WishlistItemInterface $wishlistItem */
        $wishlistItem =
            $this->get('webburza_wishlist.repository.wishlist_item')->find($request->get('id'));

        // Check if this item belongs to the current customer trying to remove it
        if ($wishlistItem->getWishlist()->getUser() != $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Remove the item from the repository
        $this->get('webburza_wishlist.repository.wishlist_item')->remove($wishlistItem);

        // If this was an AJAX request, return appropriate response
        if ($request->getRequestFormat() != 'html') {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        // Set success message
        $this->addFlash(
            'success',
            $this->get('translator')->trans('webburza_wishlist.flash.item_removed')
        );

        return $this->redirectToWishlist($wishlistItem->getWishlist());
    }

    /**
     * Add a wishlist item to a wishlist (create it).
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        // Get the current user
        if (!($user = $this->getUser())) {
            throw new BadRequestHttpException();
        }

        // Get (or create) the wishlist to which the item should be added
        $wishlist = $this->resolveWishlist($request, $user);

        // Get the product variant to be added to wishlist
        $productVariant = $this->resolveProductVariant($request);

        // Prevent duplicates
        if ($wishlist->contains($productVariant)) {
            // Set flash message
            $this->addFlash(
                'info',
                $this->get('translator')->trans('webburza_wishlist.flash.already_on_wishlist')
            );

            // Redirect back to the wishlist
            return $this->redirectToWishlist($wishlist);
        }

        /** @var WishlistItemInterface $wishlistItem */
        $wishlistItem = $this->get('webburza_wishlist.factory.wishlist_item')->createNew();
        $wishlistItem->setProductVariant($productVariant);

        $wishlist->addItem($wishlistItem);

        // Persist the wishlist item
        $this->get('webburza_wishlist.repository.wishlist_item')->add($wishlistItem);

        if ($request->getRequestFormat() != 'html') {
            return new JsonResponse(null, Response::HTTP_CREATED);
        }

        // Set success message
        $this->addFlash(
            'success',
            $this->get('translator')->trans('webburza_wishlist.flash.item_added')
        );

        return $this->redirectToWishlist($wishlist);
    }

    /**
     * Get the requested wishlist, if any,
     * or the first one for the customer.
     *
     * @param Request $request
     * @param UserInterface $user
     *
     * @return null|WishlistInterface
     */
    protected function resolveWishlist(Request $request, UserInterface $user)
    {
        // Check if a specific wishlist was requested
        if ($wishlistId = $request->get('wishlistId')) {
            $wishlist = $this->get('webburza_wishlist.repository.wishlist')
                             ->findOneBy([
                                 'id'   => $wishlistId,
                                 'user' => $user
                             ]);

            if (!$wishlist) {
                throw new BadRequestHttpException();
            }

            return $wishlist;
        }

        // If not, get the first wishlist for the customer
        $wishlist = $this->get('webburza_wishlist.repository.wishlist')->getFirstForUser($user);

        // If no wishlist found, create a new one
        if (!$wishlist) {
            $wishlist = $this->get('webburza_wishlist.factory.wishlist')->createDefault($user);
            $this->get('webburza_wishlist.repository.wishlist')->add($wishlist);
        }

        return $wishlist;
    }

    /**
     * @param Request $request
     *
     * @return mixed
     * @throws BadRequestHttpException
     */
    protected function resolveProductVariant(Request $request)
    {
        if ($productVariantId = $request->get('productVariantId')) {
            $productVariant = $this->get('sylius.repository.product_variant')
                                   ->find($productVariantId);
        } else {
            $productVariant =
                $this->container->get('webburza_wishlist.resolver.product_variant_cart')
                                ->resolve($request);
        }

        if (!$productVariant) {
            throw new BadRequestHttpException();
        }

        return $productVariant;
    }

    /**
     * @param WishlistInterface $wishlist
     *
     * @return RedirectResponse
     */
    protected function redirectToWishlist(WishlistInterface $wishlist)
    {
        // If the bundle is configured to work with a single wishlist,
        // Redirect to the the general route (without a slug)
        if (false == $this->getParameter('webburza_sylius_wishlist.multiple')) {
            return $this->redirectToRoute('webburza_frontend_wishlist_first');
        }

        // Redirect back to the wishlist
        return $this->redirectToRoute('webburza_frontend_wishlist_show', [
            'slug' => $wishlist->getSlug()
        ]);
    }
}
