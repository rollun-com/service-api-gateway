<?php
/**
 * Created by PhpStorm.
 * User: victorynox
 * Date: 13.12.17
 * Time: 12:16
 */

namespace rollun\Services\ApiGateway\Services\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\Services\ApiGateway\Services\AbstractService;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ServiceConfigAbstractFactory implements AbstractFactoryInterface
{
    const KEY = ServiceConfigAbstractFactory::class;

    const KEY_HOST = "keyHost";

    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        try {
            $config = $container->get("config");
            return (
            isset($config[static::KEY][$requestedName])
            );
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            return false;
        }
    }

    /**
     * Create an object
     * ServiceConfigAbstractFactory::KEY => [
     *      "customSimpleConfService" => "192.168.123.12",
     *      "customLargeConfService" => [
     *          ServiceConfigAbstractFactory::KEY_HOST => "192.168.123.13"
     *      ],
     * ]
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return AbstractService
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        try {
            $config = $container->get("config");
            $factoryConfig = $config[static::KEY][$requestedName];

            $host = is_array($factoryConfig) ? $factoryConfig[static::KEY_HOST] : $factoryConfig;
            return new AbstractService($host, $requestedName);
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't get config from container", $e->getCode(), $e);
        }
    }
}