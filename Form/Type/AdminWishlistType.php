<?php

namespace Webburza\Sylius\WishlistBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminWishlistType extends WishlistType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('user', UserChoiceType::class, [
            'label'       => 'webburza_wishlist.wishlist.label.user',
            'required'    => true,
            'constraints' => [
                new NotBlank()
            ]
        ]);
    }
}
