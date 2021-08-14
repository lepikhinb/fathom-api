<?php

namespace Based\Fathom;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Based\Fathom\Fathom
 */
class FathomFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fathom';
    }
}
