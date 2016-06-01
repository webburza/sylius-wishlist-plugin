<?php

namespace Webburza\Sylius\WishlistBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class BaseWishlistType extends AbstractResourceType
{
    /**
     * Build the Wishlist form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', Type\TextType::class, [
            'label' => 'webburza.sylius.wishlist.label.title'
        ]);

        $builder->add('description', Type\TextareaType::class, [
            'label' => 'webburza.sylius.wishlist.label.description'
        ]);

        $builder->add('public', Type\CheckboxType::class, [
            'label' => 'webburza.sylius.wishlist.label.public'
        ]);
    }

    public function getName()
    {
        return 'webburza_base_wishlist';
    }
}
