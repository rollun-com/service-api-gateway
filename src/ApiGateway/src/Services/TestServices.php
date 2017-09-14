<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14.09.17
 * Time: 13:36
 */

namespace rollun\Services\ApiGateway\Services;

class TestServices implements ServicesInterface
{

    /**
     * Generate string with service host
     * @return string
     */
    public function __toString()
    {
        return "google.com";
    }
}
