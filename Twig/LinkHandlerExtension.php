<?php
/**
 * Copyright (C) 2020 PrinterCare - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @copyright 2020 PrinterCare
 * @link       http://www.printer-care.de
 *
 */

declare(strict_types=1);

namespace PurrmannWebsolutions\LinkHandlerBundle\Twig;

use InvalidArgumentException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LinkHandlerExtension extends AbstractExtension
{
    /**
     * @var array
     */
    private $config;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * LinkHandlerExtension constructor.
     * @param array $config
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(array $config, UrlGeneratorInterface $urlGenerator)
    {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * getFunctions.
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('linkhandler', [$this, 'renderLink'])
        ];
    }

    /**
     * renderLink.
     * @param $entity
     * @param string $method
     * @param int $referenceType
     * @return string
     */
    public function renderLink($entity, string $method, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $this->checkIfEntityIsObject($entity);
        $class = get_class($entity);
        $this->checkIfConfigurationIsGiven($class);
        $this->checkIfMethodIsConfigured($method, $class);
        $this->checkIfRouteIsConfigured($method, $class);

        $currentDefinition = $this->config[$class][$method];
        $route = $currentDefinition['route'];
        $parameters = [];

        if (isset($currentDefinition['methodParameters']) && is_array($currentDefinition['methodParameters'])) {
            foreach ($currentDefinition['methodParameters'] as $urlParameter => $entityMethod) {
                $this->checkIfMethodExists($entity, $entityMethod, $class);
                $currentParameter = $entity->$entityMethod();
                $this->checkIfParameterTypeIsAllowed($currentParameter);
                $parameters[$urlParameter] = $currentParameter;
            }
        }

        if (isset($currentDefinition['parameters']) && is_array($currentDefinition['parameters'])) {
            foreach ($currentDefinition['parameters'] as $urlParameter => $staticParameter) {
                $this->checkIfParameterTypeIsAllowed($staticParameter);
                $parameters[$urlParameter] = $staticParameter;
            }
        }

        return $this->urlGenerator->generate($route, $parameters, $referenceType);
    }

    /**
     * checkIfEntityIsObject.
     * @param $entity
     */
    protected function checkIfEntityIsObject($entity): void
    {
        if (!is_object($entity)) {
            throw new InvalidArgumentException(sprintf('"%s" is not an object.', $entity), 1578193038149);
        }
    }

    /**
     * checkIfConfigurationIsGiven.
     * @param string $class
     */
    protected function checkIfConfigurationIsGiven(string $class): void
    {
        if (!array_key_exists($class, $this->config)) {
            throw new InvalidArgumentException(sprintf('Configuration for "%s" not exists.', $class), 1578193266262);
        }
    }

    /**
     * checkIfMethodIsConfigured.
     * @param string $method
     * @param string $class
     */
    protected function checkIfMethodIsConfigured(string $method, string $class): void
    {
        if (!array_key_exists($method, $this->config[$class])) {
            throw new InvalidArgumentException(
                sprintf('No configuration found for entity "%s" and method "%s"', $class, $method), 1578243384389
            );
        }
    }

    /**
     * checkIfRouteIsConfigured.
     * @param string $method
     * @param string $class
     */
    protected function checkIfRouteIsConfigured(string $method, string $class): void
    {
        if (!array_key_exists('route', $this->config[$class][$method])) {
            throw new InvalidArgumentException(
                sprintf('No route defined for combination of entity "%s" and method "%s"', $class, $method),
                1578193565503
            );
        }
    }

    /**
     * checkIfMethodExists.
     * @param $entity
     * @param $entityMethod
     * @param string $class
     */
    protected function checkIfMethodExists($entity, $entityMethod, string $class): void
    {
        if (!method_exists($entity, $entityMethod)) {
            throw new InvalidConfigurationException(
                sprintf('Method "%s" does not exists in entity "%s"', $entityMethod, $class),
                1578194124764
            );
        }
    }

    /**
     * checkIfTypeIsAllowed.
     * @param $currentParameter
     */
    protected function checkIfParameterTypeIsAllowed($currentParameter): void
    {
        if (!(is_string($currentParameter) || is_int($currentParameter))) {
            throw new InvalidArgumentException(
                sprintf('Only string and integer values are allowed'),
                1578194386278
            );
        }
    }
}
