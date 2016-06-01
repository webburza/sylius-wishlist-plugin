<?php

namespace Webburza\Sylius\WishlistBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class WishlistType extends BaseWishlistType
{
    /**
     * Build the Wishlist form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('customer', 'sylius_customer_choice', [
            'label' => 'webburza.sylius.wishlist.label.customer',
            'required' => true
        ]);

        parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'webburza_wishlist';
    }
}
