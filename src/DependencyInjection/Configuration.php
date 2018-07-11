<?php

declare(strict_types=1);

namespace Webburza\SyliusWishlistPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('webburza_sylius_wishlist');

        $rootNode
            ->children()
                ->booleanNode('multiple')->defaultFalse()->end()
                ->booleanNode('default_public')->defaultFalse()->end()
            ->end();

        return $treeBuilder;
    }
}
