<?php

namespace Runn\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class ServerActionAbstract implements ServerActionInterface
{

    /** @var ServerRequestInterface */
    protected $request;

    /** @var ResponseInterface */
    protected $response;

    /**
     * @param ServerRequestInterface $request
     * @return $this
     */
    public function setServerRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function setPreviousResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface
    {
        return $this->handle();
    }

    /**
     * @return ResponseInterface
     */
    abstract protected function handle(): ResponseInterface;

}
