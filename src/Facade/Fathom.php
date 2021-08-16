<?php

namespace Based\Fathom\Facade;

use Based\Fathom\Fathom as FathomManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Based\Fathom\Endpoints\AccountEndpoint account()
 * @method static \Based\Fathom\Endpoints\EventEndpoint events()
 * @method static \Based\Fathom\Endpoints\SiteEndpoint sites()
 * @method static \Based\Fathom\Endpoints\ReportEndpoint reports(null|string|\Based\Fathom\Models\Site|\Based\Fathom\Models\Event $entity = null, ?string $entityId = null)
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
