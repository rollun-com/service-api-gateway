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
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Diactoros\Request\Serializer;
use Zend\Stdlib\Parameters;
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

        $request = new Request();

        $request->setQuery(new Parameters($serverRequest->getQueryParams()));

        $request->setMethod($serverRequest->getMethod());

        $request->setFiles(new Parameters($serverRequest->getUploadedFiles()));

        $request->setContent($serverRequest->getBody());
        if($request->getCookie()) {
            foreach ($serverRequest->getCookieParams() as $cookieName => $cookieValue) {
                $request->getCookie()->set($cookieName, $cookieValue);
            }
        }

        $headers = new Headers();
        $headers->addHeaders($serverRequest->getHeaders());
        if ($serverRequest->getHeaderLine("Referer")) {

            $refererUrl = $serverRequest->getHeaderLine("Referer");
            $url = $serverRequest->getUri()->getScheme() . "://"
                . $serverRequest->getUri()->getHost();
            $port = $serverRequest->getUri()->getPort();
            $url = $port ? $url.":".$port : $url;
            $refererUrl = str_replace($url,$this->getHost($serverRequest),$refererUrl);
            $headers->addHeaderLine("Referer", $refererUrl);
        }
        $headers->addHeaderLine("Host", $this->getHost($serverRequest));
        $request->setHeaders($headers);

        $request->setUri($this->getUrl($serverRequest));

        return $request;
    }

    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    protected function getHost(ServerRequestInterface $request)
    {
        return $request->getAttribute(ServiceResolver::ATTR_SERVICE_NAME);
    }

    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    protected function getPath(ServerRequestInterface $request)
    {
        return $request->getAttribute(PathResolver::ATTR_PATH);
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getUrl(ServerRequestInterface $request)
    {
        $scheme = $request->getUri()->getScheme();
        $host = $this->getHost($request);
        $path = $this->getPath($request);
        $uri = "$scheme://" . $host . '/' . $path;
        $query = $request->getUri()->getQuery();
        if (!empty($query)) {
            $uri .= "?" . $query;
        }
        return $uri;
    }
}
