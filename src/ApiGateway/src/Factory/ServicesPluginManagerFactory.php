<?php
/**
 * Created by PhpStorm.
 * User: victorynox
 * Date: 13.12.17
 * Time: 12:38
 */

namespace rollun\Services\ApiGateway\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\Services\ApiGateway\ServicesPluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

class ServicesPluginManagerFactory implements FactoryInterface
{
    const KEY = ServicesPluginManagerFactory::class;

    /**
     * Create an object
     *  ServicesPluginManagerFactory::KEY => [
     *
     *      "aliases" => [
     *          "google" => ExampleGoogleServices::class,
     *      ],
     *      "factories" => [
     *          ExampleGoogleServices::class => InvokableFactory::class,
     *          ],
     *      ],
     *      ...
     *  ]
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        try {
            $config = $container->get("config");
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't get config from container.",$e->getCode(), $e);
        }
        $pluginManagerConfig = isset($config[static::KEY]) ? $config[static::KEY] : [];
        return new ServicesPluginManager($container, $pluginManagerConfig);
    }
}