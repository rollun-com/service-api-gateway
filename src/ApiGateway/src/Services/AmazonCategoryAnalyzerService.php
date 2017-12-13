<?php
/**
 * Created by PhpStorm.
 * User: victorynox
 * Date: 30.11.17
 * Time: 12:27
 */

namespace rollun\Services\ApiGateway\Services;


class AmazonCategoryAnalyzerService implements ServicesInterface
{
    /**
     * Generate string with service host
     * @return string
     * @deprecated
     */
    public function __toString()
    {
        return "192.168.123.27";
    }
}