<?php

namespace Webburza\Sylius\WishlistBundle\Controller\Frontend;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * Remove a wishlist item from a wishlist (delete it).
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request)
    {
        /** @var WishlistItemInterface $wishlistItem */
        $wishlistItem = $this->get('webburza.repository.wishlist_item')->find($request->get('id'));

        // Check if this item belongs to the current customer trying to remove it
        if ($wishlistItem->getWishlist()->getCustomer() != $this->getUser()->getCustomer()) {
            throw $this->createAccessDeniedException();
        }

        /** @var EntityManagerInterface $wishlistItemManager */
        $wishlistItemManager = $this->get('webburza.manager.wishlist_item');

        // Remove the wishlist item, and flush changes
        $wishlistItemManager->remove($wishlistItem);
        $wishlistItemManager->flush();

        // If this was an AJAX request, return appropriate response
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(null, 200);
        }

        // Set success message
        $this->addFlash(
            'success',
            'webburza.sylius.wishlist.item_removed'
        );

        // If the bundle is configured to work with a single wishlist,
        // Redirect to the the general route (without a slug)
        if (false == $this->getParameter('webburza.sylius.wishlist_bundle.multiple')) {
            return $this->redirectToRoute('webburza_wishlist_frontend_first');
        }

        // Redirect back to the wishlist
        return $this->redirectToRoute('webburza_wishlist_frontend_show', [
            'slug' => $wishlistItem->getWishlist()->getSlug()
        ]);
    }

    /**
     * Add a wishlist item to a wishlist (create it).
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction(Request $request)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->get('doctrine.orm.default_entity_manager');

        // Get the current customer and the wishlist to which the item should be added
        $customer = $this->getCustomer();
        $wishlist = $this->resolveWishlist($request, $customer);

        // If no wishlist found, create a new one
        if (!$wishlist) {
            $wishlist = $this->get('webburza.factory.wishlist')->createDefault($customer);
            $entityManager->persist($wishlist);
        }

        // Get the product variant to be added to wishlist
        $productVariant = $this->resolveProductVariant($request);

        // Create the Wishlist Item
        /** @var WishlistItemInterface $wishlistItem */
        $wishlistItem = $this->get('webburza.factory.wishlist_item')->createNew();
        $wishlistItem->setWishlist($wishlist);
        $wishlistItem->setProductVariant($productVariant);
        $entityManager->persist($wishlistItem);

        /** @var WishlistInterface $wishlist */
        $wishlist->addItem($wishlistItem);

        // Flush changes
        $entityManager->flush();

        // If this was an AJAX request, return appropriate response
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(null, 200);
        }

        // Set success message
        $this->addFlash(
            'success',
            'webburza.sylius.wishlist.item_added'
        );

        // If the bundle is configured to work with a single wishlist,
        // Redirect to the the general route (without a slug)
        if (false == $this->getParameter('webburza.sylius.wishlist_bundle.multiple')) {
            return $this->redirectToRoute('webburza_wishlist_frontend_first');
        }

        // Redirect back to the product
        return $this->redirectToRoute('webburza_wishlist_frontend_show', [
            'slug' => $wishlist->getSlug()
        ]);
    }

    /**
     * Resolve product variant from request,
     * using Sylius' internal resolver.
     *
     * @param Request $request
     * @return mixed
     * @throws BadRequestHttpException
     */
    protected function resolveProductVariant(Request $request)
    {
        if ($productVariantId = $request->get('product_variant_id')) {
            $productVariant =
                $this->get('sylius.repository.product_variant')->find($productVariantId);
        } else {
            $productVariant = $this->container->get('webburza.wishlist_cart_resolver')->resolve($request);
        }

        if (!$productVariant) {
            throw new BadRequestHttpException();
        }

        return $productVariant;
    }

    /**
     * Get the customer for the current user.
     *
     * @return CustomerInterface
     * @throws BadRequestHttpException
     */
    protected function getCustomer()
    {
        $customer = $this->getUser() ? $this->getUser()->getCustomer() : null;

        if (!$customer) {
            throw new BadRequestHttpException();
        }

        return $customer;
    }

    /**
     * Get the requested wishlist, if any,
     * or the first one for the customer.
     *
     * @param Request $request
     * @return WishlistInterface|null
     */
    protected function resolveWishlist(Request $request, CustomerInterface $customer)
    {
        // Check if a specific wishlist was requested
        if ($wishlistId = $request->get('wishlist_id')) {
            $wishlist = $this->get('webburza.repository.wishlist')
                        ->findForCustomer($customer, $wishlistId);

            if (!$wishlist) {
                throw new BadRequestHttpException();
            }
        }

        // If not, get the first wishlist for the customer
        return $this->get('webburza.repository.wishlist')->getFirstForCustomer($customer);
    }
}
