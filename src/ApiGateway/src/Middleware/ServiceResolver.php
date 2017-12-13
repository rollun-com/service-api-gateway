<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12.09.17
 * Time: 16:24
 */

namespace rollun\Services\ApiGateway\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\logger\Exception\LoggedException;
use rollun\Services\ApiGateway\ServicesPluginManager;
use Zend\ServiceManager\ServiceManager;

class ServiceResolver implements MiddlewareInterface
{
    const DEFAULT_GW_PATH = "/";

    const ATTR_SERVICE_NAME = "serviceName";

    /**
     * @var ServicesPluginManager
     */
    private $servicesLocator;

    /**
     * ResponseSender constructor.
     * @param ServicesPluginManager $servicesLocator
     */
    public function __construct(ServicesPluginManager $servicesLocator)
    {
        $this->servicesLocator = $servicesLocator;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $host = $request->getUri()->getHost();

        $serviceName = $this->getServiceName($host);

        $service = $this->getService($serviceName);
        $request = $request->withAttribute(static::ATTR_SERVICE_NAME, $service);
        $response = $delegate->process($request);
        return ($response);
    }

    /**
     * @param $host
     * @return string
     * @throws LoggedException
     */
    protected function getServiceName($host)
    {
        $pattern = '/(?<name>[\w_-]+)\./';
        if (!preg_match($pattern, $host, $math)) {
            throw new LoggedException("$host is not service");
        }
        return ($math['name']);
    }

    /**
     * @param $serviceName
     * @return mixed
     * @throws LoggedException
     */
    protected function getService($serviceName)
    {
        if (!$this->servicesLocator->has($serviceName)) {
            throw new LoggedException("Service $serviceName not found");
        }
        $host = $this->servicesLocator->get($serviceName);
        return $host;
    }
}
