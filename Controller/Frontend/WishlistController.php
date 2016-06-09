<?php

namespace Webburza\Sylius\WishlistBundle\Controller\Frontend;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;

class WishlistController extends FOSRestController
{
    /**
     * WishlistController constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * Get the first publicly visible wishlist for the current user.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function firstAction(Request $request)
    {
        // Require logged-in user
        if (!$this->getUser()) {
            throw new NotFoundHttpException();
        }

        // Get the first wishlist for the user
        $wishlist = $this->get('webburza.repository.wishlist')->getFirstForCustomer($this->getUser()->getCustomer());

        // Throw exception if not found
        if (!$wishlist) {
            throw new NotFoundHttpException();
        }

        // If the bundle is configured to work with multiple wishlists
        // Redirect to the current wishlist to update URI
        if ($this->getParameter('webburza.sylius.wishlist_bundle.multiple')) {
            return $this->redirectToRoute('webburza_wishlist_frontend_show', [
                'slug' => $wishlist->getSlug()
            ]);
        }

        // If this was an AJAX request, return appropriate response
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['wishlist' => $wishlist], 200);
        }

        // Create the view for the wishlist
        $view = View::create();

        // Set view
        $view
            ->setTemplate('WebburzaSyliusWishlistBundle:Frontend/Wishlist:show.html.twig')
            ->setData([
                'wishlist' => $wishlist
            ])
        ;

        // Handle the view
        return $this->handleView($view);
    }

    /**
     * Get a publicly visible wishlist by slug.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->get('webburza.repository.wishlist')->findOneBy(['slug' => $request->get('slug')]);

        // If the wishlist is not public,
        // then only the owner and administrators can access it
        if (!$this->userCanAccessWishlist($this->getUser(), $wishlist)) {
            throw new AccessDeniedHttpException(
                $this->get('translator')->trans('webburza.sylius.wishlist.frontend.not_public')
            );
        }

        // If this was an AJAX request, return appropriate response
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(null, 200);
        }

        // Create the view for the wishlist
        $view = View::create();

        // Set view
        $view
            ->setTemplate('WebburzaSyliusWishlistBundle:Frontend/Wishlist:show.html.twig')
            ->setData([
                'wishlist' => $wishlist
            ])
        ;

        // Handle the view
        return $this->handleView($view);
    }

    /**
     * Clear all items on a wishlist.
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function clearAction(Request $request, $id)
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->get('webburza.repository.wishlist')->findForCustomer(
            $this->getUser()->getCustomer(), $id
        );

        // Check if wishlist found
        if (!$wishlist) {
            $this->createNotFoundException();
        }

        /** @var EntityManagerInterface $wishlistItemManager */
        $wishlistItemManager = $this->get('webburza.manager.wishlist_item');

        // Remove each wishlist item
        foreach ($wishlist->getItems() as $wishlistItem) {
            $wishlistItemManager->remove($wishlistItem);
        }
        $wishlistItemManager->flush();

        // If this was an AJAX request, return appropriate response
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(null, 200);
        }

        // Set success message
        $this->addFlash(
            'success',
            'webburza.sylius.wishlist.cleared'
        );

        // If the bundle is configured to work with a single wishlist,
        // Redirect to the the general route (without a slug)
        if (false == $this->getParameter('webburza.sylius.wishlist_bundle.multiple')) {
            return $this->redirectToRoute('webburza_wishlist_frontend_first');
        }

        // If this was an plain HTTP request, redirect back to the wishlist
        return $this->redirectToRoute('webburza_wishlist_frontend_show', [
            'slug' => $wishlist->getSlug()
        ]);
    }

    /**
     * Check if a wishlist is publicly available, or the
     * user has special privileges to access it.
     *
     * @param $user
     * @param $wishlist
     * @return bool
     */
    protected function userCanAccessWishlist(UserInterface $user = null, WishlistInterface $wishlist)
    {
        if ($wishlist->isPublic()) {
            return true;
        }

        if ($user) {
            if (
                $user->hasRole('ROLE_ADMINISTRATION_ACCESS') ||
                (
                    $user->getCustomer() &&
                    $user->getCustomer()->getId() == $wishlist->getCustomer()->getId()
                )
            ) {
                return true;
            }
        }

        return false;
    }
}
