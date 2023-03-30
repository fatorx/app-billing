<?php

namespace Application\Util;

class Environment
{
    /**
     * @param $name
     * @return mixed|string
     */
    public static function env($name): mixed
    {
        return $_ENV[$name] ?? 'not-set';
    }
}
