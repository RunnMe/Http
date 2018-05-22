<?php

namespace Runn\tests\Http\Uri;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Runn\Http\Exceptions\EmptyActions;
use Runn\Http\Exceptions\InvalidRequest;
use Runn\Http\ServerActionAbstract;
use Runn\Http\ServerActions;
use Runn\tests\Http\ActionStubs\Request;
use Runn\tests\Http\ActionStubs\Response;

class ServerActionsTest extends TestCase
{
    public function testActions()
    {
        $request = new Request('RequestBody');

        $action1 = new class extends ServerActionAbstract
        {
            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface|null $previousResponse
             * @return ResponseInterface
             */
            protected function handle(
                ServerRequestInterface $request,
                ResponseInterface $previousResponse = null
            ): ResponseInterface {
                if (null === $previousResponse) {
                    return new Response($request->getBody() . '+Action_1_Body');
                }
                return new Response($previousResponse->getBody() . '+Action_1_Body');
            }
        };

        $action2 = new class extends ServerActionAbstract
        {
            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface|null $previousResponse
             * @return ResponseInterface
             */
            protected function handle(
                ServerRequestInterface $request,
                ResponseInterface $previousResponse = null
            ): ResponseInterface {
                if (null === $previousResponse) {
                    return new Response($request->getBody() . '+Action_2_Body');
                }
                return new Response($previousResponse->getBody() . '+Action_2_Body');
            }
        };

        $action3 = new class extends ServerActionAbstract
        {
            /**
             * @param ServerRequestInterface $request
             * @param ResponseInterface|null $previousResponse
             * @return ResponseInterface
             */
            protected function handle(
                ServerRequestInterface $request,
                ResponseInterface $previousResponse = null
            ): ResponseInterface {
                if (null === $previousResponse) {
                    return new Response($request->getBody() . '+Action_3_Body');
                }
                return new Response($previousResponse->getBody() . '+Action_3_Body');
            }
        };

        $actions = new ServerActions([$action1, $action3]);
        $actions->setAsLastAction($action2);
        $actions->setAsFirstAction($action3);

        $response = $actions($request);

        $this->assertSame(
            'RequestBody+Action_3_Body+Action_1_Body+Action_3_Body+Action_2_Body',
            (string)$response->getBody()
        );
    }

    public function testEmptyActions()
    {
        $request = new Request('');
        $actions = new ServerActions([]);
        $this->expectException(EmptyActions::class);
        $actions($request);
    }

    /*@7.1*/
    /*    public function testEmptyRequest()
    {
        $actions = new ServerActions([]);
        $this->expectException(InvalidRequest::class);
        $response = $actions(null);
    }*/
}
