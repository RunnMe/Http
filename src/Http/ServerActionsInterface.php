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
    public function setAsFirstAction(ServerActionInterface $action)/*@7.1*//*: void*/;

    /**
     * @param ServerActionInterface $action
     */
    public function setAsLastAction(ServerActionInterface $action)/*@7.1*//*: void*/;
}
