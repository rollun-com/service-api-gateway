<?php

namespace rollun\test\ApiGatewayTest;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\datastore\DataStore\Memory;
use rollun\mesh\DataStore\Interfaces\MeshInterface;
use rollun\Services\ApiGateway\MeshServicePluginManager;
use rollun\Services\ApiGateway\Services\ServicesInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class MeshServicePluginManagerTest extends TestCase
{

    /** @var MeshServicePluginManager */
    protected $object;

    /** @var MeshInterface */
    protected $meshDataStore;

    public function setUp()
    {
        $this->meshDataStore = new class extends Memory implements MeshInterface
        {
        };
        $this->object = new MeshServicePluginManager($this->meshDataStore);
        $this->initMeshDataStore();
    }


    /**
     * @return array
     */
    protected function getDefaultHosts()
    {
        return [
            "service" => "host",
            "192.168.123.13" => "186.75.32.123",
            "data" => "192.168.123.13",
            "google" => "google.com",
            "localhost" => "127.0.0.1:8080"
        ];
    }

    /**
     * @param array $hosts
     */
    protected function initMeshDataStore($hosts = [])
    {
        if (empty($hosts)) {
            $hosts = $this->getDefaultHosts();
        }
        foreach ($hosts as $name => $host) {
            $this->meshDataStore->create([
                $this->meshDataStore->getIdentifier() => uniqid(),
                MeshInterface::FIELD_SERVICE_NAME => $name,
                MeshInterface::FIELD_SERVICE_HOST => $host,
            ]);
        }
    }


    /**
     * @return array
     */
    public function hesDataProvider()
    {
        return [
            ["service", true],
            ["data", true],
            ["google", true],
            ["localhost", true],
            ["localhost", true],
            ["192.168.123.13", true],
        ];
    }

    /**
     *
     */
    public function getNotFoundDataProvider()
    {
        return [
            [""],
        ];
    }

    /**
     *
     */
    public function getFoundDataProvider()
    {
        return [
            ["service", "host"],
            ["192.168.123.13", "186.75.32.123"],
            ["data", "192.168.123.13"],
            ["google", "google.com"],
            ["localhost", "127.0.0.1:8080"],
        ];
    }

    /**
     * @param $name
     * @param $host
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @dataProvider getFoundDataProvider
     */
    public function testGetSuccess($name, $host)
    {
        /** @var ServicesInterface $service */
        $service = $this->object->get($name);
        $this->assertTrue($service instanceof ServicesInterface);
        $this->assertEquals($host, $service->__toString());
    }

    /**
     * @param $name
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @dataProvider getNotFoundDataProvider
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testGetNotFound($name)
    {
        $this->object->get($name);
    }

    /**
     * @param $name
     * @param $has
     * @dataProvider hesDataProvider
     */
    public function testHas($name, $has)
    {
        $result = $this->object->has($name);
        $this->assertEquals($has, $result);
    }
}
