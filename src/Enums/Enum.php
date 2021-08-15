<?php

namespace Based\Fathom\Enums;

use ReflectionClass;

class Enum
{
    public static function values(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }
}
