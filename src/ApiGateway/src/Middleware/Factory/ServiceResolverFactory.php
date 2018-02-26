<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14.09.17
 * Time: 12:21
 */

namespace rollun\Services\ApiGateway\Middleware\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use rollun\Services\ApiGateway\Middleware\ServiceResolver;
use rollun\Services\ApiGateway\ServicesPluginManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class ServiceResolverFactory implements FactoryInterface
{
    const KEY = ServiceResolverFactory::class;

    const KEY_HOST_SERVICE_PLUGIN_MANAGER = "hostServicePluginManager";

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get("config");
        if(!isset($config[static::KEY])) {
            throw new ServiceNotCreatedException("Not found config for service $requestedName.");
        }
        $factoryConfig = $config[static::KEY];
        $hostServicesPluginManager = $container->get($factoryConfig[static::KEY_HOST_SERVICE_PLUGIN_MANAGER]);
        return new ServiceResolver($hostServicesPluginManager);
    }
}
