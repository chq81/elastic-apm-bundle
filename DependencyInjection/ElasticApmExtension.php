<?php

namespace Chq81\ElasticApmBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 */
class ElasticApmExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $elasticApmAgentServiceDefinition = $container->getDefinition('elastic_apm.service.agent');
        $elasticApmAgentServiceDefinition->replaceArgument(0, $config);

        if (array_key_exists('httpClient', $config) && !empty($config['httpClient'])) {
            $elasticApmAgentServiceDefinition->replaceArgument(1, new Reference($config['httpClient']));
        }

        if (array_key_exists('logger', $config) && !empty($config['logger'])) {
            $elasticApmAgentServiceDefinition->replaceArgument(2, new Reference($config['logger']));
        }
    }
}
