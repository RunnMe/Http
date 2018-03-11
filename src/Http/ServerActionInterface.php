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
     * @return $this
     */
    public function setServerRequest(ServerRequestInterface $request);

    /**
     * @param ResponseInterface $response
     * @return $this;
     */
    public function setPreviousResponse(ResponseInterface $response);

    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface;

}
