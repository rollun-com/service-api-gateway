<?php


namespace rollun\Services\ApiGateway;


use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\mesh\DataStore\Interfaces\MeshInterface;
use rollun\Services\ApiGateway\Services\AbstractService;
use rollun\Services\ApiGateway\Services\ServicesInterface;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Query;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\PluginManagerInterface;

class MeshServicePluginManager implements PluginManagerInterface
{
    /**
     * @var MeshInterface
     */
    protected $meshDataStore;

    /**
     * MeshServicePluginManager constructor.
     * @param MeshInterface $meshDataStore
     */
    public function __construct(MeshInterface $meshDataStore)
    {
        $this->meshDataStore = $meshDataStore;
    }

    /**
     * @param $serviceName
     * @return string
     */
    protected function resolveServiceHost(string $serviceName)
    {
        $query = new Query();
        $query->setQuery(new EqNode(MeshInterface::FIELD_SERVICE_NAME, $serviceName));
        $result = $this->meshDataStore->query($query);
        return empty($result) ? null : current($result)[MeshInterface::FIELD_SERVICE_HOST];
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        $serviceHost = $this->resolveServiceHost($id);
        if (is_null($serviceHost)) {
            throw new ServiceNotFoundException("Host for service with name $id not found.");
        }
        $service = $this->build($id, [
            "host" => $serviceHost
        ]);
        $this->validate($service);
        return $service;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return !empty($this->resolveServiceHost($id));
    }

    /**
     * Validate an instance
     *
     * @param  object $instance
     * @return void
     * @throws InvalidServiceException If created instance does not respect the
     *     constraint on type imposed by the plugin manager
     * @throws ContainerException if any other error occurs
     */
    public function validate($instance)
    {
        if (!$instance instanceof ServicesInterface) {
            throw new InvalidServiceException();
        }
    }

    /**
     * Build a service by its name, using optional options (such services are NEVER cached).
     *
     * @param  string $name
     * @param  null|array $options
     * @return mixed
     * @throws Exception\ServiceNotFoundException If no factory/abstract
     *     factory could be found to create the instance.
     * @throws Exception\ServiceNotCreatedException If factory/delegator fails
     *     to create the instance.
     * @throws ContainerExceptionInterface if any other error occurs
     */
    public function build($name, array $options = null)
    {
        if (!isset($options["host"])) {
            throw new ServiceNotCreatedException("For service $name host not found.");
        }
        $service = new AbstractService($options["host"], $name);
        return $service;
    }
}