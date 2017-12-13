<?php
/**
 * Created by PhpStorm.
 * User: victorynox
 * Date: 13.12.17
 * Time: 12:19
 */

namespace rollun\Services\ApiGateway\Services;


class AbstractService implements ServicesInterface
{
    /**
     * Service host name
     * @var string
     */
    protected $host;

    /**
     * Service name in gateway
     * @var string
     */
    protected $serviceName;

    /**
     * AbstractService constructor.
     * @param string $host
     * @param string $serviceName
     */
    public function __construct(string $host, string $serviceName)
    {
        $this->host = $host;
        $this->serviceName = $serviceName;
    }

    /**
     * Generate string with service host
     * @return string
     */
    public function __toString()
    {
        return $this->host;
    }
}