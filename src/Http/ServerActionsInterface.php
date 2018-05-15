<?php

namespace Runn\Http;

/**
 * Interface ServerActionsInterface
 * @package Runn\Http
 */
interface ServerActionsInterface extends ServerActionInterface
{
    /**
     * @param ServerActionInterface $action
     */
    public function setAsFirstAction(ServerActionInterface $action): void;

    /**
     * @param ServerActionInterface $action
     */
    public function setAsLastAction(ServerActionInterface $action): void;
}
