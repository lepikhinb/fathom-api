<?php

namespace Based\Fathom\Endpoints;

use Based\Fathom\Api;
use Based\Fathom\Collections\EventCollection;
use Based\Fathom\Models\Event;

class EventEndpoint
{
    protected ?string $cursor = null;

    public function __construct(
        protected Api $api
    ) {
    }

    /**
     * Return a list of all events this site owns.
     * 
     * @param  string  $siteId  The ID of the site you wish to load events for.
     * @param  int  $limit  A limit on the number of objects to be returned, between 1 and 100.
     * @param  bool  $next  Paginate request
     * @return EventCollection|Event[]
     * 
     * @throws \Based\Fathom\Exceptions\AuthenticationException 
     * @throws \Exception 
     */
    public function get(string $siteId, int $limit = 10, bool $next = false): EventCollection
    {
        $data = $this->api->get("sites/{$siteId}/events", [
            'starting_after' => $next ? $this->cursor : null,
            'limit' => $limit,
        ])->json('data');

        $collection = new EventCollection($data);
        $this->cursor = $collection->last()->id;

        return $collection;
    }

    /**
     * Return a single event
     * 
     * @param  string  $siteId  The ID of the site that the event belongs to. This is the same string you use in the tracking code.
     * @param  string  $eventId  The ID of the event you wish to track. You have to create this event first before sending us completions.
     * @return \Based\Fathom\Models\Event 
     * 
     * @throws \Based\Fathom\Exceptions\AuthenticationException 
     * @throws \Exception 
     */
    public function getEvent(string $siteId, string $eventId): Event
    {
        $data = $this->api->get("sites/{$siteId}/events/{$eventId}")->json();

        return new Event(...$data);
    }

    /**
     * Create an event
     * 
     * @param  string  $siteId  The ID of the site you wish to create an event for.
     * @param  string  $name  The name of the webevent. Any string (up to 255 characters) is acceptable, and it doesn't have to match the webevent URL
     * @return \Based\Fathom\Models\Event 
     * 
     * @throws \Based\Fathom\Exceptions\AuthenticationException 
     * @throws \Exception 
     */
    public function create(string $siteId, string $name): Event
    {
        $data = $this->api->post("sites/{$siteId}/events", [
            'name' => $name,
        ])->json();

        return new Event(...$data);
    }

    /**
     * Update an event
     * 
     * @param  string  $siteId  The ID of the site that the event belongs to.
     * @param  string  $eventId  The ID of the event you wish to track. You have to create this event first before sending us completions.
     * @param  string  $name  The name of the webevent. Any string (up to 255 characters) is acceptable, and it doesn't have to match the webevent URL
     * @return \Based\Fathom\Models\Event 
     * 
     * @throws \Based\Fathom\Exceptions\AuthenticationException 
     * @throws \Exception 
     */
    public function update(string $siteId, string $eventId, string $name): Event
    {
        $data = $this->api->post("name/{$siteId}/events/{$eventId}", [
            'name' => $name,
        ])->json();

        return new Event(...$data);
    }

    /**
     * Wipe all pageviews & event completions from a webevent. This would typically we used when you want to completely reset statistics or right before you launch a webevent (to remove test data).
     * 
     * @param  string  $siteId  The ID of the site that the event belongs to.
     * @param  string  $eventId  The ID of the event you wish to wipe.
     * @return void 
     * 
     * @throws \Based\Fathom\Exceptions\AuthenticationException 
     * @throws \Exception 
     */
    public function wipe(string $siteId, string $eventId): void
    {
        $this->api->delete("sites/{$siteId}/events/{$eventId}");
    }

    /**
     * Delete a event (careful, you can't undo this)
     * 
     * @param  string  $siteId  The ID of the site that the event belongs to.
     * @param  string  $eventId  The ID of the event you wish to delete.
     * @return void 
     * 
     * @throws \Based\Fathom\Exceptions\AuthenticationException 
     * @throws \Exception 
     */
    public function delete(string $siteId, string $eventId): void
    {
        $this->api->delete("sites/{$siteId}/events/{$eventId}");
    }
}
