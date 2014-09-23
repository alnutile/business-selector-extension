<?php

namespace OrangeDigital\BusinessSelectorExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

use Behat\Behat\Extension\ExtensionInterface;

/**
 * Extension hooks into the Behat DIC and exposes the extensions Initializer.
 * 
 * @author Ben Waine <ben.waine@orange.com>
 * @author Phill Hicks <phillip.hicks@orange.com>  
 */
class Extension implements ExtensionInterface {

    /**
     * Loads a specific configuration.
     *
     * @param array            $config    Extension configuration hash (from behat.yml)
     * @param ContainerBuilder $container ContainerBuilder instance
     * 
     * @return void
     */
    public function load(array $config, ContainerBuilder $container)
    {
        
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__)
        );
        
        $loader->load('services.xml');
        
        $container->setParameter('businessselector.parameters', $config);   
    }

    /**
     * Setups configuration for current extension.
     *
     * @param ArrayNodeDefinition $builder
     * 
     * @return void
     */
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder->
            children()->
                scalarNode('urlFilePath')->
                    defaultNull()->
                end()->
                scalarNode('selectorFilePath')->
                    defaultNull()->
                end()->
                scalarNode('assetPath')->
                    defaultNull()->
                end()->
                scalarNode('timeout')->
                    defaultNull()->
                end()->
                arrayNode('contexts')->
                    children()->
                        scalarNode('UIBusinessSelector')->
                            defaultNull()->
                        end()->
                    end()->
                end()->
            end();
                
    }

    /**
     * Returns compiler passes used by this extension.
     *
     * @return array
     */
    public function getCompilerPasses()
    {
        return array();
    }
}

return new Extension();