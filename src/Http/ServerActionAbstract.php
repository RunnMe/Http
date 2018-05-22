<?php

namespace Runn\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class ServerActionAbstract implements ServerActionInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $previousResponse
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $previousResponse = null
    ): ResponseInterface {
        return $this->handle($request, $previousResponse);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $previousResponse
     * @return ResponseInterface
     */
    abstract protected function handle(
        ServerRequestInterface $request,
        ResponseInterface $previousResponse = null
    ): ResponseInterface;
}
