<?php
declare(strict_types=1);

namespace PurrmannWebsolutions\LinkHandlerBundle\Tests\DependencyInjection;


use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PurrmannWebsolutions\LinkHandlerBundle\DependencyInjection\PurrmannWebsolutionsLinkHandlerExtension;
use PurrmannWebsolutions\LinkHandlerBundle\Tests\Fixtures\FixtureClass;
use PurrmannWebsolutions\LinkHandlerBundle\Twig\LinkHandlerExtension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

class PurrmannWebsolutionsLinkHandlerExtensionTest extends AbstractExtensionTestCase
{

    public function testLinkHandlerExtensionServiceHasGivenConfig()
    {
        $config = Yaml::parse(file_get_contents(__DIR__ . '/../Fixtures/Configuration/config.yml'));

        $this->load($config['pw_linkhandler']);
        $this->assertContainerBuilderHasService(LinkHandlerExtension::class);
    }

    /**
     * @inheritDoc
     */
    protected function getContainerExtensions(): array
    {
        return [
            new PurrmannWebsolutionsLinkHandlerExtension()
        ];
    }
}
