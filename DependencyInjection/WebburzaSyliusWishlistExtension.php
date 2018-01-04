<?php

namespace Webburza\Sylius\WishlistBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class WebburzaSyliusWishlistExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set config parameters
        $container->setParameter('webburza_sylius_wishlist.multiple', $config['multiple']);
        $container->setParameter('webburza_sylius_wishlist.default_public', $config['default_public']);
        $container->setParameter('webburza_sylius_wishlist.account_manageable', $config['account_manageable']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
