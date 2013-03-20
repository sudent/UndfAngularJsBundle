<?php

namespace Undf\AngularJsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author Dani Gonzalez <daniel.gonzalez@undefined.es>
 */
class UndfAngularJsExtension extends Extension
{

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configs = $this->fixConfiguration($configs);

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('undf_angular_js.catalogue', empty($config['catalogue']) ? array() : $config['catalogue']);
        $container->setParameter('undf_angular_js.module_sets', empty($config['module_sets']) ? array() : $config['module_sets']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    public function fixConfiguration(array $configs)
    {
        $defaultConfig = array(
            'module_sets' => array(
                'novendors' => array(),
                'default' => array(
                    'vendors' => array(
                        'ngResource',
                        'ui',
                        'ui.bootstrap'
                    )
                ),
                'all' => array(
                    'vendors' => '*',
                    'undf' => '*'
                )
            ),
            'catalogue' => $this->getDefaultCatalogue()
        );
        $configs = array_merge($defaultConfig, $configs[0]);

        foreach ($configs['module_sets'] as $name => $set) {
            foreach ($set as $parent => $modules) {
                if (!isset($configs['catalogue'][$parent])) {
                    throw new \Exception(sprintf('Module "%s" not found in the catalogue.', $parent));
                }
                if (is_array($modules)) {
                    foreach ($modules as $module) {
                        if (!isset($configs['catalogue'][$parent][$module])) {
                            throw new \Exception(sprintf('Module "%s.%s" not found in the catalogue.', $parent, $module));
                        }
                    }
                } elseif (trim($modules) == '*') {
                    $configs['module_sets'][$name][$parent] = array_keys($configs['catalogue'][$parent]);
                } else {
                    if (!isset($configs['catalogue'][$parent][$modules])) {
                        throw new \Exception(sprintf('Module "%s.%s" not found in the catalogue.', $parent, $modules));
                    }
                }
            }
        }
        return array($configs);
    }

    private function getDefaultCatalogue()
    {
        $yaml = new \Symfony\Component\Yaml\Parser();
        return $yaml->parse(file_get_contents(__DIR__ . '/../Resources/config/catalogue.yml'));
    }

}
