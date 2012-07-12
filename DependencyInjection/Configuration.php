<?php

namespace webultd\Payu\PaymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('webultd_payu_payment');

        $rootNode
            ->children()
                ->scalarNode('file')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('environment')->defaultValue('sandbox')->end()
                ->scalarNode('merchant_pos_id')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('pos_auth_key')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('client_id')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('client_secret')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('signature_key')->isRequired()->cannotBeEmpty()->end()
            ->end();

        $this->addShoppingCartSection($rootNode);

        return $treeBuilder;
    }

    private function addShoppingCartSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('shopping_cart')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('tax')->defaultValue(23)->end()
                    ->end()
            ->end();
    }
}
