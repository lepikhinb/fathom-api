<?php

namespace Based\Fathom;

use Based\Fathom\Endpoints\AccountEndpoint;
use Based\Fathom\Endpoints\EventEndpoint;
use Based\Fathom\Endpoints\ReportEndpoint;
use Based\Fathom\Endpoints\SiteEndpoint;
use Based\Fathom\Models\Event;
use Based\Fathom\Models\Site;

class Fathom
{
    public Api $api;
    protected AccountEndpoint $account;
    protected SiteEndpoint $sites;
    protected EventEndpoint $events;
    protected ReportEndpoint $reports;

    public function __construct(string $token)
    {
        $this->api = new Api($token);
    }

    public function sites(): SiteEndpoint
    {
        return $this->sites ??= new SiteEndpoint($this->api);
    }

    public function account(): AccountEndpoint
    {
        return $this->account ??= new AccountEndpoint($this->api);
    }

    public function events(): EventEndpoint
    {
        return $this->events ??= new EventEndpoint($this->api);
    }

    public function reports(null | string | Site | Event $entity = null, ?string $entityId = null): ReportEndpoint
    {
        return $this->reports ??= new ReportEndpoint($this->api, $entity, $entityId);
    }
}
