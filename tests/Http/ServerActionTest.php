<?php

namespace Runn\tests\Http\Uri;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Runn\Http\ServerActionAbstract;
use Runn\tests\Http\ActionStubs\Request;
use Runn\tests\Http\ActionStubs\Response;

class ServerActionTest extends TestCase
{
    public function testAction()
    {
        $request = new Request('RequestBody');

        $action = new class extends ServerActionAbstract
        {
            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface $previousResponse
             * @return ResponseInterface
             */
            protected function handle(
                ServerRequestInterface $request,
                ResponseInterface $previousResponse = null
            ): ResponseInterface {
                return new Response($request->getBody() . '+ActionBody');
            }
        };

        $response = $action($request);

        $this->assertSame('RequestBody+ActionBody', (string)$response->getBody());
    }
}
