<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12.09.17
 * Time: 18:33
 */

namespace rollun\Services\ApiGateway\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\logger\Exception\LoggedException;
use rollun\Services\ApiGateway\Middleware\ServiceResolver;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceManager;

class RequestSender implements MiddlewareInterface
{
    const ATTR_SERVICE_RESPONSE = "serviceResponse";

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
        /** @var Request $sendRequest */
        $sendRequest = $request->getAttribute(RequestResolver::ATTR_SEND_REQUEST);
        $sendRequest->setUri($this->getUrl($request));

        $client = new Client();
        $response = $client->send($sendRequest);
        //$client = new Client("http://www.google.com/");
        //$response = $client->send();
        $request = $request->withAttribute(static::ATTR_SERVICE_RESPONSE, $response);
        $response = $delegate->process($request);
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getUrl(ServerRequestInterface $request)
    {
        $host = $request->getAttribute(ServiceResolver::ATTR_SERVICE_NAME);
        $path = $request->getAttribute(PathResolver::ATTR_PATH);
        $uri = "http://" . $host . '/' . $path;
        return $uri;
    }
}
