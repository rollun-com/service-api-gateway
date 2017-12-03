<?php
/**
 * Created by PhpStorm.
 * User: victorynox
 * Date: 22.11.17
 * Time: 19:10
 */

namespace rollun\Services\ApiGateway\Services;


class CatalogViewerService implements ServicesInterface
{

    /**
     * Generate string with service host
     * @return string
     */
    public function __toString()
    {
        return "192.168.123.128";
    }
}