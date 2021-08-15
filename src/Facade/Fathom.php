<?php

namespace Based\Fathom\Facade;

use Based\Fathom\Fathom as FathomManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Based\Fathom\Endpoints\AccountEndpoint account()
 * @method static \Based\Fathom\Endpoints\EventEndpoint events()
 * @method static \Based\Fathom\Endpoints\SiteEndpoint sites()
 *
 * @see \Based\Fathom\Fathom
 */
class Fathom extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FathomManager::class;
    }
}
