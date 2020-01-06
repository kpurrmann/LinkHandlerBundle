<?php

declare(strict_types=1);

namespace PurrmannWebsolutions\LinkHandlerBundle\DependencyInjection;

use PurrmannWebsolutions\LinkHandlerBundle\Twig\LinkHandlerExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class PurrmannWebsolutionsLinkHandlerExtension
 * @copyright 2020 Kevin Purrmann
 */
class PurrmannWebsolutionsLinkHandlerExtension extends Extension
{

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.xml');

        $defintion = $container->getDefinition(LinkHandlerExtension::class);
        $defintion->setArgument('config', $config['entities']);
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }
}
