<?php

namespace Webburza\Sylius\WishlistBundle\Controller\Account;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webburza\Sylius\WishlistBundle\Form\Type\WishlistType;
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
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        // Throw 404 if not in multiple wishlist mode
        if (!$this->getParameter('webburza_sylius_wishlist.multiple')) {
            return $this->redirectToRoute('sylius_shop_account_dashboard');
        }

        // Get all wishlists for the current user
        $wishlists = $this->get('webburza_wishlist.repository.wishlist')->findBy([
            'user' => $this->getUser()
        ], [
            'createdAt' => 'asc'
        ]);

        $view = View::create([
            'wishlists' => $wishlists
        ]);

        $view->setTemplate('WebburzaSyliusWishlistBundle:Frontend/Account/Wishlist:index.html.twig');

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        // Throw 404 if not in multiple wishlist mode
        if (!$this->getParameter('webburza_sylius_wishlist.multiple')) {
            throw new NotFoundHttpException();
        }

        // Get wishlist form and handle request
        $form = $this->get('form.factory')->create(WishlistType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var WishlistInterface $wishlist */
            $wishlist = $form->getData();

            // Set wishlist user
            $wishlist->setUser($this->getUser());

            // Persist changes
            $this->get('webburza_wishlist.repository.wishlist')->add($wishlist);

            // If this was an AJAX request, return appropriate response
            if ($request->getRequestFormat() != 'html') {
                return new JsonResponse(null, Response::HTTP_CREATED);
            }

            // Set success message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('webburza_wishlist.flash.updated')
            );

            return $this->redirectToRoute('webburza_account_wishlist_edit', [
                'id' => $wishlist->getId()
            ]);
        }

        $view = View::create([
            'form' => $form->createView()
        ]);

        $view->setTemplate('WebburzaSyliusWishlistBundle:Frontend/Account/Wishlist:create.html.twig');

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateAction(Request $request)
    {
        // Get the wishlist
        $wishlist = $this->get('webburza_wishlist.repository.wishlist')->findOneBy([
            'id'   => $request->get('id'),
            'user' => $this->getUser()
        ]);

        if (!$wishlist) {
            throw new NotFoundHttpException();
        }

        // Get wishlist form
        $form = $this->get('form.factory')->create(WishlistType::class, $wishlist);

        if (in_array($request->getMethod(), ['PUT', 'POST'])) {
            if ($form->handleRequest($request)->isValid()) {

                // Persist changes
                $this->get('webburza_wishlist.repository.wishlist')->add($form->getData());

                // If this was an AJAX request, return appropriate response
                if ($request->getRequestFormat() != 'html') {
                    return new JsonResponse(null, Response::HTTP_OK);
                }

                // Set success message
                $this->addFlash(
                    'success',
                    $this->get('translator')->trans('webburza_wishlist.flash.updated')
                );

                return $this->redirectToRoute('webburza_account_wishlist_edit', [
                    'id' => $wishlist->getId()
                ]);
            }
        }

        $view = View::create([
            'form'     => $form->createView(),
            'wishlist' => $wishlist
        ]);

        $view->setTemplate('WebburzaSyliusWishlistBundle:Frontend/Account/Wishlist:update.html.twig');

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        $wishlist = $this->get('webburza_wishlist.repository.wishlist')->findOneBy([
            'id'   => $request->get('id'),
            'user' => $this->getUser()
        ]);

        // Throw exception if not found
        if (!$wishlist) {
            throw new NotFoundHttpException();
        }

        // Remove the wishlist
        $this->get('webburza_wishlist.repository.wishlist')->remove($wishlist);

        // If this was an AJAX request, return appropriate response
        if ($request->getRequestFormat() != 'html') {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        // Set success message
        $this->addFlash(
            'success',
            $this->get('translator')->trans('webburza_wishlist.ui.deleted')
        );

        return $this->redirectToRoute('webburza_account_wishlist_index');
    }
}
