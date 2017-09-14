<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12.09.17
 * Time: 16:23
 */

namespace rollun\Services\ApiGateway\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\logger\Exception\LoggedException;

class PathResolver implements MiddlewareInterface
{

    const ATTR_PATH = "path";

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
        $fullPath = $request->getUri()->getPath();
        $path = $this->getPath($fullPath);
        $request = $request->withAttribute(static::ATTR_PATH, $path);
        $response = $delegate->process($request);
        return $response;
    }

    /**
     * @param $path
     * @return mixed
     * @throws LoggedException
     */
    protected function getPath($path)
    {
        $pattern = '/^\/[\w_]+\/(?<path>[\w\W]+)/';
        if (!preg_match($pattern, $path, $match)) {
            //throw new LoggedException("Path not found");
            return "";
        }
        return $match['path'];
    }
}
