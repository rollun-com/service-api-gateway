<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14.09.17
 * Time: 12:18
 */

namespace rollun\Services\ApiGateway;

use rollun\Services\ApiGateway\Services\BCatalogService;
use rollun\Services\ApiGateway\Services\ServicesInterface;
use rollun\Services\ApiGateway\Services\GoogleServices;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Factory\InvokableFactory;

class ServicesPluginManager extends AbstractPluginManager
{

    protected $aliases = [
        "google" => GoogleServices::class,
        "bcatalog" => BCatalogService::class,
    ];

    protected $factories = [
        GoogleServices::class => InvokableFactory::class,
        BCatalogService::class => InvokableFactory::class
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
