<?php

namespace Webburza\Sylius\WishlistBundle\Controller\Frontend;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;

class WishlistController extends FOSRestController
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * Get a publicly visible wishlist by slug.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->get('webburza_wishlist.repository.wishlist')->findOneBy([
            'slug' => $request->get('slug')
        ]);

        // Check if wishlist exists, and it can be accessed
        if (!($wishlist && $this->userCanAccessWishlist($this->getUser(), $wishlist))) {
            throw new NotFoundHttpException();
        }

        $view = View::create($wishlist);

        if ($request->getRequestFormat() == 'html') {
            $view->setTemplate('WebburzaSyliusWishlistBundle:Frontend/Wishlist:show.html.twig');

            $view->setData([
                'wishlist' => $wishlist
            ]);
        }

        return $this->handleView($view);
    }

    /**
     * Get the first publicly visible wishlist for the current user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function firstAction(Request $request)
    {
        if (!$this->getUser()) {
            throw new NotFoundHttpException();
        }

        // Get the first wishlist for the user
        $wishlist =
            $this->get('webburza_wishlist.repository.wishlist')->getFirstForUser($this->getUser());

        // Create a wishlist if none exist
        if (!$wishlist) {
            $wishlist = $this->get('webburza_wishlist.factory.wishlist')->createDefault($this->getUser());
            $this->get('webburza_wishlist.repository.wishlist')->add($wishlist);
        }

        // If the bundle is configured to work with multiple wishlists
        // Redirect to the current wishlist to update URI
        if ($this->getParameter('webburza_sylius_wishlist.multiple')) {
            return $this->redirectToRoute('webburza_frontend_wishlist_show', [
                'slug' => $wishlist->getSlug()
            ]);
        }

        // If this was an AJAX request, return appropriate response
        if ($request->getRequestFormat() != 'html') {
            return new JsonResponse(['wishlist' => $wishlist], 200);
        }

        // Create the view for the wishlist
        $view = View::create([
            'wishlist' => $wishlist
        ]);

        // Set view template
        $view->setTemplate('WebburzaSyliusWishlistBundle:Frontend/Wishlist:show.html.twig');

        // Handle the view
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function clearAction(Request $request)
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->get('webburza_wishlist.repository.wishlist')->findOneBy([
            'id' => $request->get('id'),
            'user' => $this->getUser()
        ]);

        // Check if wishlist found
        if (!$wishlist) {
            $this->createNotFoundException();
        }

        // Remove each wishlist item
        foreach ($wishlist->getItems() as $wishlistItem) {
            $this->get('webburza_wishlist.repository.wishlist_item')->remove($wishlistItem);
        }

        // If this was an AJAX request, return appropriate response
        if ($request->getRequestFormat() != 'html') {
            return new JsonResponse(null, 200);
        }

        // Set success message
        $this->addFlash(
            'success',
            $this->get('translator')->trans('webburza_wishlist.flash.cleared')
        );

        return $this->redirectToWishlist($wishlist);
    }

    /**
     * Check if a wishlist is publicly available, or the
     * user has special privileges to access it.
     *
     * @param $user
     * @param $wishlist
     *
     * @return bool
     */
    protected function userCanAccessWishlist(
        UserInterface $user = null,
        WishlistInterface $wishlist
    ) {
        return $wishlist->isPublic() || ($user && $user->getId() == $wishlist->getUser()->getId());
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
