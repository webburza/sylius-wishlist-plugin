<?php

namespace Webburza\Sylius\WishlistBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class WishlistType extends AbstractResourceType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', Type\TextType::class, [
            'label'       => 'webburza_wishlist.wishlist.label.title',
            'required'    => true,
            'constraints' => [
                new NotBlank()
            ]
        ]);

        $builder->add('description', Type\TextareaType::class, [
            'label'    => 'webburza_wishlist.wishlist.label.description',
            'required' => false
        ]);

        $builder->add('public', Type\CheckboxType::class, [
            'label'    => 'webburza_wishlist.wishlist.label.public',
            'required' => false
        ]);
    }
}
