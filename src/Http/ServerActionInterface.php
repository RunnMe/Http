<?php

namespace Runn\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ServerActionInterface
 * @package Runn\Http
 */
interface ServerActionInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $previousResponse
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $previousResponse = null
    ): ResponseInterface;
}
