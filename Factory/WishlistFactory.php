<?php

namespace Webburza\Sylius\WishlistBundle\Factory;

use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class WishlistFactory extends Factory
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var bool
     */
    protected $defaultPublic;

    /**
     * WishlistFactory constructor.
     *
     * @param $className
     * @param TranslatorInterface $translator
     * @param $defaultPublic
     */
    public function __construct($className, TranslatorInterface $translator, $defaultPublic)
    {
        parent::__construct($className);

        $this->translator = $translator;
        $this->defaultPublic = $defaultPublic;
    }

    public function createDefault(CustomerInterface $customer)
    {
        // Create a new wishlist
        $wishlist = $this->createNew();

        // Set default title
        $wishlist->setTitle(
            $this->translator
                 ->trans('webburza.sylius.wishlist.default_title')
        );

        // Set default public state
        $wishlist->setPublic($this->defaultPublic);

        // Set customer
        $wishlist->setCustomer($customer);

        return $wishlist;
    }
}
