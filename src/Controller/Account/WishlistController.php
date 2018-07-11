<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Webburza\SyliusWishlistPlugin\Form\Type\WishlistType;
use Webburza\SyliusWishlistPlugin\Model\WishlistInterface;
use Webburza\SyliusWishlistPlugin\Provider\LoggedInUserProviderInterface;
use Webburza\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;

class WishlistController extends Controller
{
    /**
     * @var WishlistRepositoryInterface
     */
    protected $wishlistRepository;

    /**
     * @var LoggedInUserProviderInterface
     */
    protected $loggedInUserProvider;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var bool
     */
    protected $multipleWishlistMode;

    /**
     * @param WishlistRepositoryInterface $wishlistRepository
     * @param LoggedInUserProviderInterface $loggedInUserProvider
     * @param TranslatorInterface $translator
     * @param FormFactoryInterface $formFactory
     * @param bool $multipleWishlistMode
     */
    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        LoggedInUserProviderInterface $loggedInUserProvider,
        TranslatorInterface $translator,
        FormFactoryInterface $formFactory,
        bool $multipleWishlistMode
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->translator = $translator;
        $this->formFactory = $formFactory;
        $this->multipleWishlistMode = $multipleWishlistMode;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request) : Response
    {
        // Throw 404 if not in multiple wishlist mode
        if (!$this->multipleWishlistMode) {
            return $this->redirectToRoute('sylius_shop_account_dashboard');
        }

        // Get all wishlists for the current user
        $wishlists = $this->wishlistRepository->findBy([
            'user' => $this->loggedInUserProvider->getUser()
        ], [
            'createdAt' => 'asc'
        ]);

        // Render view
        return $this->render('@WebburzaSyliusWishlistPlugin/Resources/views/Account/index.html.twig', [
            'wishlists' => $wishlists
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request) : Response
    {
        // Throw 404 if not in multiple wishlist mode
        if (!$this->multipleWishlistMode) {
            throw $this->createNotFoundException();
        }

        // Get wishlist form and handle request
        $form = $this->formFactory->create(WishlistType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var WishlistInterface $wishlist */
            $wishlist = $form->getData();

            // Set wishlist user
            $wishlist->setUser($this->loggedInUserProvider->getUser());

            // Persist changes
            $this->wishlistRepository->add($wishlist);

            // If this was an AJAX request, return appropriate response
            if ($request->getRequestFormat() != 'html') {
                return new JsonResponse(null, Response::HTTP_CREATED);
            }

            // Set success message
            $this->addFlash(
                'success', $this->translator->trans('webburza_sylius_wishlist.flash.updated')
            );

            return $this->redirectToRoute('webburza_sylius_wishlist_account_wishlist_edit', [
                'id' => $wishlist->getId()
            ]);
        }

        return $this->render('@WebburzaSyliusWishlistPlugin/Resources/views/Account/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateAction(Request $request) : Response
    {
        // Get the wishlist
        $wishlist = $this->wishlistRepository->findOneBy([
            'id'   => $request->get('id'),
            'user' => $this->loggedInUserProvider->getUser()
        ]);

        if (!$wishlist) {
            throw $this->createNotFoundException();
        }

        // Get wishlist form
        $form = $this->formFactory->create(WishlistType::class, $wishlist);

        if (in_array($request->getMethod(), ['PUT', 'POST'])) {
            if ($form->handleRequest($request)->isValid()) {
                /** @var WishlistInterface $wishlist */
                $wishlist = $form->getData();

                // Set wishlist user
                $wishlist->setUser($this->loggedInUserProvider->getUser());

                // Persist changes
                $this->wishlistRepository->add($wishlist);

                // If this was an AJAX request, return appropriate response
                if ($request->getRequestFormat() != 'html') {
                    return new JsonResponse(null, Response::HTTP_OK);
                }

                // Set success message
                $this->addFlash(
                    'success', $this->translator->trans('webburza_sylius_wishlist.flash.updated')
                );

                return $this->redirectToRoute('webburza_sylius_wishlist_account_wishlist_edit', [
                    'id' => $wishlist->getId()
                ]);
            }
        }

        return $this->render('@WebburzaSyliusWishlistPlugin/Resources/views/Account/update.html.twig', [
            'form'     => $form->createView(),
            'wishlist' => $wishlist
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request) : Response
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findOneBy([
            'id'   => $request->get('id'),
            'user' => $this->loggedInUserProvider->getUser()
        ]);

        // Throw exception if not found
        if (!$wishlist) {
            throw $this->createNotFoundException();
        }

        // Remove the wishlist
        $this->wishlistRepository->remove($wishlist);

        // If this was an AJAX request, return appropriate response
        if ($request->getRequestFormat() != 'html') {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        // Set success message
        $this->addFlash(
            'success', $this->translator->trans('webburza_sylius_wishlist.ui.deleted')
        );

        return $this->redirectToRoute('webburza_sylius_wishlist_account_wishlist_index');
    }
}
