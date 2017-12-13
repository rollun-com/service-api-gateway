<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14.09.17
 * Time: 12:18
 */

namespace rollun\Services\ApiGateway;

use rollun\Services\ApiGateway\Services\CatalogViewerService;
use rollun\Services\ApiGateway\Services\Factory\ServiceConfigAbstractFactory;
use rollun\Services\ApiGateway\Services\ServicesInterface;
use rollun\Services\ApiGateway\Services\ExampleGoogleServices;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Factory\InvokableFactory;

class ServicesPluginManager extends AbstractPluginManager
{

    protected $aliases = [
    ];

    protected $factories = [

    ];

    protected $abstractFactories = [
        ServiceConfigAbstractFactory::class
    ];

    protected $instanceOf = ServicesInterface::class;

    /**
     * @param $instance
     * @throws RuntimeException
     */
    public function validatePlugin($instance)
    {
        try {
            $this->validate($instance);
        } catch (InvalidServiceException $exception) {
            throw new RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @inheritdoc
     * @param object $instance
     * @throws InvalidServiceException
     */
    public function validate($instance)
    {
        if (!$instance instanceof ServicesInterface) {
            throw new InvalidServiceException(sprintf(
                '%s can only create instances of %s; %s is invalid',
                static::class,
                ServicesInterface::class,
                is_object($instance) ? get_class($instance) : gettype($instance)
            ));
        }
    }
}
