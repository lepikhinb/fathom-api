<?php

use Based\Fathom\Collections\EventCollection;
use Based\Fathom\Models\Event;
use Illuminate\Http\Client\Request;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertTrue;

test('get event', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response(eventsDataset()[0], 200),
    ]);

    $event = fathom()->events()->getEvent('CDBUGS', 'purchase-early-access');

    assertInstanceOf(Event::class, $event);
    assertEquals('purchase-early-access', $event->id);
});

test('get events', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response([
            'data' => eventsDataset(),
        ], 200),
    ]);

    $events = fathom()->events()->get('CDBUGSCDBUGS');

    assertInstanceOf(EventCollection::class, $events);
    assertInstanceOf(Event::class, $events->first());
});

test('pagination', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => function (Request $request) {
            return httpClient()->response([
                'data' => [$request->data()['starting_after'] ? eventsDataset()[0] : eventsDataset()[1]],
            ], 200);
        },
    ]);

    $event1 = fathom()->events()->get('CDBUGS', 1)->first();
    $event2 = fathom()->events()->get('CDBUGS', 1, true)->first();

    assertNotEquals($event2->name, $event1->name);
});

test('update event', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response(eventsDataset()[0], 200),
    ]);

    $event = fathom()->events()->update('CDBUGS', 'purchase-early-access', 'name', 'none');

    assertInstanceOf(Event::class, $event);
    assertEquals('purchase-early-access', $event->id);
});

test('delete event', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response([], 200),
    ]);

    fathom()->events()->delete('CDBUGS', 'purchase-early-access');

    assertTrue(fathom()->api->latestResponse->successful());
});

test('wipe event', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response([], 200),
    ]);

    fathom()->events()->wipe('CDBUGS', 'purchase-early-access');

    assertTrue(fathom()->api->latestResponse->successful());
});
