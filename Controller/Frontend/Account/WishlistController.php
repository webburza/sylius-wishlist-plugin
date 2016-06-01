<?php

namespace Webburza\Sylius\WishlistBundle\Controller\Frontend\Account;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webburza\Sylius\WishlistBundle\Form\Type\BaseWishlistType;
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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // Throw 404 if not in multiple wishlist mode
        if (!$this->getParameter('webburza.sylius.wishlist_bundle.multiple')) {
            return $this->redirectToRoute('sylius_account_profile_show');
        }

        // Get all wishlists for the current user
        $customer = $this->getUser()->getCustomer();
        $wishlists = $this->get('webburza.repository.wishlist')->getWishlistsForCustomer($customer);

        $view = $this
            ->view()
            ->setTemplate('WebburzaSyliusWishlistBundle:Frontend/Account/Wishlist:index.html.twig')
            ->setData([
                'wishlists' => $wishlists
            ])
        ;

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        // Throw 404 if not in multiple wishlist mode
        if (!$this->getParameter('webburza.sylius.wishlist_bundle.multiple')) {
            throw new NotFoundHttpException();
        }

        // Get wishlist form
        $form = $this->get('form.factory')->create('webburza_base_wishlist');

        // Check if data submitted
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            // Check if form valid
            if ($form->isValid()) {
                /** @var WishlistInterface $wishlist */
                $wishlist = $form->getData();
                $wishlist->setCustomer($this->getUser()->getCustomer());

                // Persist the wishlist
                $this->get('webburza.manager.wishlist')->persist($wishlist);
                $this->get('webburza.manager.wishlist')->flush();

                // If this was an AJAX request, return appropriate response
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(null, 200);
                }

                // Set success message
                $this->addFlash(
                    'success',
                    'webburza.sylius.wishlist.created'
                );

                return $this->redirectToRoute('webburza_wishlist_account_index');
            }
        }

        $view = $this
            ->view()
            ->setTemplate('WebburzaSyliusWishlistBundle:Frontend/Account/Wishlist:create.html.twig')
            ->setData([
                'form' => $form->createView()
            ])
        ;

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request)
    {
        // Get wishlist id
        $wishlistId = $request->get('id');

        // Get the wishlist
        $customer = $this->getUser()->getCustomer();
        $wishlist = $this->get('webburza.repository.wishlist')->findForCustomer($customer, $wishlistId);

        // Get wishlist form
        $form = $this->get('form.factory')->create('webburza_base_wishlist', $wishlist);

        if ($request->isMethod('PUT')) {
            if ($form->submit($request)->isValid()) {
                // Persist changes
                $this->get('webburza.manager.wishlist')->flush();

                // If this was an AJAX request, return appropriate response
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(null, 200);
                }

                // Set success message
                $this->addFlash(
                    'success',
                    'webburza.sylius.wishlist.updated'
                );

                return $this->redirectToRoute('webburza_wishlist_account_index');
            }
        }

        $view = $this
            ->view()
            ->setTemplate('WebburzaSyliusWishlistBundle:Frontend/Account/Wishlist:update.html.twig')
            ->setData([
                'form' => $form->createView(),
                'wishlist' => $wishlist
            ])
        ;

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request)
    {
        // Get wishlist id
        $wishlistId = $request->get('id');

        // Get the wishlist to delete
        $customer = $this->getUser()->getCustomer();
        $wishlist = $this->get('webburza.repository.wishlist')->findForCustomer($customer, $wishlistId);

        // Throw exception if not found
        if (!$wishlist) {
            throw new NotFoundHttpException();
        }

        // Remove the wishlist
        $this->get('webburza.manager.wishlist')->remove($wishlist);
        $this->get('webburza.manager.wishlist')->flush();

        // If this was an AJAX request, return appropriate response
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(null, 200);
        }

        // Set success message
        $this->addFlash(
            'success',
            'webburza.sylius.wishlist.deleted'
        );

        return $this->redirectToRoute('webburza_wishlist_account_index');
    }
}
