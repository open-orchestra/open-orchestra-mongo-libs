<?php

namespace OpenOrchestra\MongoBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpenOrchestraMongoExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('metadata_search_reader.yml');
        $loader->load('filter_strategy.yml');
        $loader->load('transformer.yml');

        //cache metadata
        $cacheDirectory = $config['search_metadata']['cache_dir'];
        $cacheDirectory = $container->getParameterBag()->resolveValue($cacheDirectory);
        if (!file_exists($cacheDirectory)) {
            if (!@mkdir($cacheDirectory, 0777, true)) {
                throw new \RuntimeException(sprintf('Could not create cache directory "%s".', $cacheDirectory));
            }
        }

        $container->getDefinition('open_orchestra.annotation_cache')
                  ->replaceArgument(0, $cacheDirectory);

        //metadata directories config
        $bundles = $container->getParameter('kernel.bundles');
        $directories = array();
        if ($config['search_metadata']['auto_detection']) {
            foreach ($bundles as $name => $class) {
                $ref = new \ReflectionClass($class);
                $directories[$ref->getNamespaceName()] = dirname($ref->getFileName()).'/Resources/config/search';
            }
        }
        foreach ($config['search_metadata']['directories'] as $directory) {
            $directory['path'] = rtrim(str_replace('\\', '/', $directory['path']), '/');
            if ('@' === $directory['path'][0]) {
                $bundleName = substr($directory['path'], 1, strpos($directory['path'], '/') - 1);
                $ref = new \ReflectionClass($bundles[$bundleName]);
                $directory['path'] = dirname($ref->getFileName()).substr($directory['path'], strlen('@'.$bundleName));
            }
            $directories[rtrim($directory['namespace_prefix'], '\\')] = rtrim($directory['path'], '\\/');
        }

        $container
            ->getDefinition('open_orchestra.annotation_file_locator')
            ->replaceArgument(0, $directories)
        ;
    }
}
