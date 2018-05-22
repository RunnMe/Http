<?php

namespace Runn\Http;

use Runn\Core\TypedCollection;

/**
 * Class ActionsCollection
 * @package Runn\Http
 */
class ActionsCollection extends TypedCollection
{
    /**
     * @return string
     */
    public static function getType()
    {
        return ServerActionInterface::class;
    }
}
