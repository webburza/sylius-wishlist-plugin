<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class WebburzaSyliusWishlistExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        // Process configuration
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);

        // Set configuration as a parameters in the container
        $container->setParameter('webburza_sylius_wishlist.config.multiple', $config['multiple']);
        $container->setParameter('webburza_sylius_wishlist.config.default_public', $config['default_public']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');
    }
}
