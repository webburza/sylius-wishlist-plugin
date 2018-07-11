<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\Resolver;

use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductVariantFromRequestResolver implements ProductVariantFromRequestResolverInterface
{
    /**
     * @var ProductVariantRepositoryInterface
     */
    protected $productVariantRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var AddToCartCommandFactoryInterface
     */
    protected $addToCartCommandFactory;

    /**
     * @var CartContextInterface
     */
    protected $cartContext;

    /**
     * @var CartItemFactoryInterface
     */
    protected $cartItemFactory;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param ProductVariantRepositoryInterface $productVariantRepository
     * @param ProductRepositoryInterface $productRepository
     * @param AddToCartCommandFactoryInterface $addToCartCommandFactory
     * @param CartContextInterface $cartContext
     * @param CartItemFactoryInterface $cartItemFactory
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductRepositoryInterface $productRepository,
        AddToCartCommandFactoryInterface $addToCartCommandFactory,
        CartContextInterface $cartContext,
        CartItemFactoryInterface $cartItemFactory,
        FormFactoryInterface $formFactory
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->productRepository = $productRepository;
        $this->addToCartCommandFactory = $addToCartCommandFactory;
        $this->cartContext = $cartContext;
        $this->cartItemFactory = $cartItemFactory;
        $this->formFactory = $formFactory;
    }

    /**
     * @param Request $request
     *
     * @return ProductVariantInterface
     */
    public function resolve(Request $request) : ProductVariantInterface
    {
        if ($productVariantId = $request->get('productVariantId')) {
            $productVariant = $this->productVariantRepository->find($productVariantId);
        } else {
            $productVariant = $this->resolveViaCartForm($request);
        }

        if (!$productVariant) {
            throw new BadRequestHttpException();
        }

        return $productVariant;
    }

    /**
     * @param Request $request
     *
     * @return ProductVariantInterface
     */
    protected function resolveViaCartForm(Request $request) : ?ProductVariantInterface
    {
        /** @var ProductInterface $product */
        if (!($product = $this->productRepository->find($request->get('productId')))) {
            throw new BadRequestHttpException();
        }

        // Create the add-to-cart command to receive resolved product variant
        $addToCartCommand = $this->addToCartCommandFactory->createWithCartAndCartItem(
            $this->cartContext->getCart(),
            $this->cartItemFactory->createForProduct($product)
        );

        // We use forms to help resolve the variant
        $this->formFactory->create(AddToCartType::class, $addToCartCommand, [
            'product' => $product
        ])->submit($request->request->all()['sylius_add_to_cart']);

        /** @var OrderItemInterface $orderItem */
        $orderItem = $addToCartCommand->getCartItem();

        return $orderItem->getVariant();
    }
}
