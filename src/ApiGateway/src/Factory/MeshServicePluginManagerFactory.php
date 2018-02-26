<?php


namespace rollun\Services\ApiGateway\Factory;


use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\Services\ApiGateway\MeshServicePluginManager;
use rollun\Services\ApiGateway\ServicesPluginManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class MeshServicePluginManagerFactory implements FactoryInterface
{
    const KEY = MeshServicePluginManagerFactory::class;

    const KEY_MESH_DATASTORE = "meshDataStore";

    /**
     * Create an object
     *  MeshServicePluginManagerFactory::KEY => [
     *    MeshServicePluginManagerFactory::KEY_MESH_DATASTORE => MeshInterface::class
     *  ]
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        try {
            $config = $container->get("config");
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't get config from container.",$e->getCode(), $e);
        }
        if(!isset($config[static::KEY])){
            throw new ServiceNotCreatedException("Not found config for service $requestedName.");
        }
        $pluginManagerConfig = $config[static::KEY];

        $meshDataStore = $container->get($pluginManagerConfig[static::KEY_MESH_DATASTORE]);
        return new MeshServicePluginManager($meshDataStore);
    }
}