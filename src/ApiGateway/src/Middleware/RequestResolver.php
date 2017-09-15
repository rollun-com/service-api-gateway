<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.09.17
 * Time: 10:51
 */

namespace rollun\Services\ApiGateway\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Http\Request;
use Zend\Diactoros\Request\Serializer;
use Zend\Uri\Http;

class RequestResolver implements MiddlewareInterface
{

    const ATTR_SEND_REQUEST = 'sendRequest';

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
        $sendRequest = $this->requestCreator($request);
        $request = $request->withAttribute(static::ATTR_SEND_REQUEST, $sendRequest);
        $response = $delegate->process($request);
        return $response;
    }

    /**
     * @deprecated
     * @param ServerRequestInterface $serverRequest
     * @return Request
     */
    protected function requestCreator(ServerRequestInterface $serverRequest)
    {
        $stringRequest  = Serializer::toString($serverRequest);
        $request = Request::fromString($stringRequest);
        return $request;
    }
}
