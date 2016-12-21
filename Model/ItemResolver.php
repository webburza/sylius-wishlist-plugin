<?php

namespace Webburza\Sylius\WishlistBundle\Model;

use Sylius\Bundle\InventoryBundle\Validator\Constraints\InStock;
use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Cart\Resolver\ItemResolvingException;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Item resolver for wishlist bundle.
 * Returns product variant for a submitted order cart item.
 *
 * We use this class only to allow adding out-of-stock items to wishlist,
 * so we can re-use the current 'Add to cart' forms.
 */
class ItemResolver implements ItemResolverInterface
{

    /**
     * @var FactoryInterface
     */
    protected $cartItemFactory;

    /**
     * @var RepositoryInterface
     */
    protected $productRepository;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * ItemResolver constructor.
     *
     * @param FactoryInterface $cartItemFactory
     * @param RepositoryInterface $productRepository
     * @param FormFactoryInterface $formFactory
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(
        FactoryInterface               $cartItemFactory,
        RepositoryInterface            $productRepository,
        FormFactoryInterface           $formFactory,
        ChannelContextInterface        $channelContext
    ) {
        $this->cartItemFactory = $cartItemFactory;
        $this->productRepository = $productRepository;
        $this->formFactory = $formFactory;
        $this->channelContext = $channelContext;
    }
    
    /**
     * @param Request $request
     * @return CartItemInterface
     */
    public function resolve(Request $request)
    {
        if (!($id = $request->get('id'))) {
            throw new ItemResolvingException('Error while trying to add item to cart.');
        }

        // Create cart item
        $item = $this->cartItemFactory->createNew();

        $channel = $this->channelContext->getChannel();
        if (!$product = $this->productRepository->findOneByIdAndChannel($id, $channel)) {
            throw new ItemResolvingException('Requested product was not found.');
        }

        // We use forms to easily set the quantity and pick variant,
        // the same way Sylius does it while resolving products when adding to cart
        $form = $this->formFactory->create('sylius_cart_item', $item, array('product' => $product));
        $form->submit($request);

        // Get appropriate variant
        $variant = $item->getVariant() ? $item->getVariant() : $product->getFirstVariant();

        return $variant;
    }
}
