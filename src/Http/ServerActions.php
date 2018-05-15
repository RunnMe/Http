<?php

namespace Runn\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Runn\Http\Exceptions\EmptyActions;
use Runn\Http\Exceptions\InvalidRequest;

/**
 * Class ServerActions
 * @package Runn\Http
 */
final class ServerActions implements ServerActionsInterface
{
    private $actions;

    /**
     * ServerActions constructor.
     * @param array $actions
     */
    public function __construct(array $actions)
    {
        $this->actions = new ActionsCollection($actions);
    }

    /**
     * @param ServerRequestInterface|null $request
     * @return ResponseInterface
     * @throws EmptyActions
     * @throws InvalidRequest
     */
    public function __invoke(?ServerRequestInterface $request): ResponseInterface
    {
        if (null === $request) {
            throw new InvalidRequest('Request is empty.');
        }

        if ($this->actions->empty()) {
            throw new EmptyActions('Action list is empty.');
        }

        $response = $request;
        /** @var ServerActionInterface $action */
        foreach ($this->actions as $action) {
            /** @noinspection NullPointerExceptionInspection */
            $response = $action($response);
        }

        return $response;
    }

    /**
     * @param ServerActionInterface $action
     */
    public function setAsFirstAction(ServerActionInterface $action): void
    {
        $this->actions->prepend($action);
    }

    /**
     * @param ServerActionInterface $action
     */
    public function setAsLastAction(ServerActionInterface $action): void
    {
        $this->actions->append($action);
    }
}
