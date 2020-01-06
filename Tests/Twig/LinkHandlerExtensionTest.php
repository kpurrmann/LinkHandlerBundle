<?php

declare(strict_types=1);

namespace PurrmannWebsolutions\LinkHandlerBundle\Tests\Twig;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PurrmannWebsolutions\LinkHandlerBundle\Tests\Fixtures\FixtureClass;
use PurrmannWebsolutions\LinkHandlerBundle\Twig\LinkHandlerExtension;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkHandlerExtensionTest extends TestCase
{

    /**
     * @var MockObject|UrlGeneratorInterface
     */
    protected $urlGenerator;

    protected function setUp():void
    {
        $this->urlGenerator = $this->getMockBuilder(UrlGenerator::class)
            ->disableOriginalConstructor()
            ->setMethods(['generate'])
            ->getMock();
    }

    /**
     * testException.
     */
    public function testExceptionWhenClassNotConfigured()
    {
        $this->expectException(\InvalidArgumentException::class);
        $linkHandler = new LinkHandlerExtension($this->getStandardConfig(), $this->urlGenerator);
        $linkHandler->renderLink(new FixtureClass(), 'detail');
    }

    /**
     * testExceptionWhenMethodNotConfigured.
     */
    public function testExceptionWhenMethodNotConfigured()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1578243384389);
        $linkHandler = new LinkHandlerExtension($this->getStandardConfig(), $this->urlGenerator);
        $linkHandler->renderLink(new \stdClass(), 'method_not_configured');
    }

    public function testExceptionWhenNoObjectGiven()
    {
        $this->expectExceptionCode(1578193038149);
        $linkHandler = new LinkHandlerExtension($this->getStandardConfig(), $this->urlGenerator);
        $linkHandler->renderLink('string', 'detail');
    }

    /**
     * testExceptionWhenMethodNotFoundInObject.
     */
    public function testExceptionWhenMethodNotFoundInObject()
    {
        $this->expectExceptionCode(1578194124764);
        $config = [
            FixtureClass::class => [
                'detail' => [
                    'route' => 'test_route',
                    'methodParameters' => ['id' => 'getId']
                ]
            ]
        ];
        $linkHandler = new LinkHandlerExtension($config, $this->urlGenerator);
        $fixture = new FixtureClass();
        $linkHandler->renderLink($fixture, 'detail');
    }

    public function testExceptionWhenMethodReturnsNotAllowedType()
    {
        $this->expectExceptionCode(1578194386278);
        $config = [
            FixtureClass::class => [
                'detail' => [
                    'route' => 'test_route',
                    'methodParameters' => ['id' => 'getArray']
                ]
            ]
        ];
        $linkHandler = new LinkHandlerExtension($config, $this->urlGenerator);
        $fixture = new FixtureClass();
        $linkHandler->renderLink($fixture, 'detail');
    }

    /**
     * testExceptionWhenRouteIsNotGiven.
     */
    public function testExceptionWhenRouteIsNotGiven()
    {
        $this->expectExceptionCode(1578193565503);
        $config = ['stdClass' => ['detail' => []]];
        $linkHandler = new LinkHandlerExtension($config, $this->urlGenerator);
        $linkHandler->renderLink(new \stdClass(), 'detail');
    }

    /**
     * getStandardConfig.
     * @return array
     */
    public function getStandardConfig(): array
    {
        return [
            'stdClass' => [
                'detail' => [
                    'route' => 'test_route'
                ]
            ]
        ];
    }


    /**
     * testGenerationOfLink.
     * @param array $config
     * @param mixed $fixture
     * @param string $method
     * @param string $expectedRoute
     * @param array $expectedParameters
     * @dataProvider getSeveralConfigs()
     */
    public function testGenerationOfLink(
        array $config,
        $fixture,
        string $method,
        string $expectedRoute,
        array $expectedParameters
    ) {
        $this->urlGenerator->expects($this->once())->method('generate')->with($expectedRoute, $expectedParameters);
        $linkHandler = new LinkHandlerExtension($config, $this->urlGenerator);
        $linkHandler->renderLink($fixture, $method);
    }

    /**
     * getSeveralConfigs.
     * @return \Generator
     */
    public function getSeveralConfigs(): \Generator
    {
        yield [
            [FixtureClass::class => ['detail' => ['route' => 'test_route']]],
            new FixtureClass(),
            'detail',
            'test_route',
            []
        ];

        yield [
            [FixtureClass::class => ['detail' => ['route' => 'test_route', 'methodParameters' => ['id' => 'getString']]]],
            new FixtureClass(),
            'detail',
            'test_route',
            ['id' => 'string']
        ];

        yield [
            [FixtureClass::class => ['detail' => ['route' => 'test_route', 'parameters' => ['id' => 1]]]],
            new FixtureClass(),
            'detail',
            'test_route',
            ['id' => 1]
        ];
    }
}
