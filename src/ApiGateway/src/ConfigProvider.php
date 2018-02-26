<?php

namespace rollun\Services\ApiGateway;

use Psr\Container\ContainerInterface;
use rollun\actionrender\Factory\MiddlewarePipeAbstractFactory;
use rollun\Services\ApiGateway\Factory\ServicesPluginManagerFactory;
use rollun\Services\ApiGateway\Middleware\Factory\GatewayRouterFactory;
use rollun\Services\ApiGateway\Middleware\Factory\ServiceResolverFactory;
use rollun\Services\ApiGateway\Middleware\GatewayRouter;
use rollun\Services\ApiGateway\Middleware\ServiceResolver;
use rollun\Services\ApiGateway\Middleware\PathResolver;
use rollun\Services\ApiGateway\Middleware\RequestResolver;
use rollun\Services\ApiGateway\Middleware\RequestSender;
use rollun\Services\ApiGateway\Middleware\ResponseDecoder;
use rollun\Services\ApiGateway\Services\CatalogViewerService;
use rollun\Services\ApiGateway\Services\ExampleGoogleServices;
use SebastianBergmann\ObjectEnumerator\Exception;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    const API_GATEWAY_SERVICE = "ApiGatewayPipe";

    const HOST_SERVICE_PLUGIN_MANAGER = "hostServicePluginManager";

    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            MiddlewarePipeAbstractFactory::KEY => $this->getPipeConfig(),
            ServiceResolverFactory::KEY => $this->getServiceResolverFactoryConfig()
        ];
    }

    /**
     * @return array
     */
    protected function getDependencies()
    {
        return [
            'aliases' => $this->getAliases(),
            'factories' => $this->getFactories()
        ];
    }

    /**
     * @return array
     */
    protected function getAliases()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getFactories()
    {
        return [
            GatewayRouter::class => GatewayRouterFactory::class,
            ServiceResolver::class => ServiceResolverFactory::class,
            PathResolver::class => InvokableFactory::class,
            RequestResolver::class => InvokableFactory::class,
            RequestSender::class => InvokableFactory::class,
            ResponseDecoder::class => InvokableFactory::class,
            ServicesPluginManager::class => ServicesPluginManagerFactory::class,

        ];
    }

    /**
     * @return array
     */
    protected function getPipeConfig()
    {
        return [
            static::API_GATEWAY_SERVICE => [
                MiddlewarePipeAbstractFactory::KEY_MIDDLEWARES => [
                    ServiceResolver::class,
                    PathResolver::class,
                    RequestResolver::class,
                    RequestSender::class,
                    ResponseDecoder::class,
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getServiceResolverFactoryConfig()
    {
        return [
            ServiceResolverFactory::KEY_HOST_SERVICE_PLUGIN_MANAGER => static::HOST_SERVICE_PLUGIN_MANAGER
        ];
    }
}
