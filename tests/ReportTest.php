<?php

use Based\Fathom\Enums\Aggregate;
use Based\Fathom\Enums\Entity;
use Based\Fathom\Enums\FilterProperty;
use Based\Fathom\Exceptions\IncorrectValueException;
use Based\Fathom\Exceptions\MissingValueException;
use Based\Fathom\Models\Event;
use Based\Fathom\Models\Site;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

beforeEach(function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response(['data' => []], 200),
    ]);
});

test('report for a site', function () {
    $site = new Site('test', 'test', 'none');

    $request = fathom()->reports()->for($site)->aggregate(Aggregate::VISITS);
    $request->get();

    assertEquals(Entity::PAGEVIEW, $request->query()['entity']);
    assertEquals('test', $request->query()['entity_id']);
    assertTrue(fathom()->api->latestResponse->successful());
});

test('pass entity to constructor', function () {
    $site = new Site('test', 'test', 'none');

    $request = fathom()->reports($site)->aggregate(Aggregate::VISITS);
    $request->get();

    assertEquals(Entity::PAGEVIEW, $request->query()['entity']);
    assertEquals('test', $request->query()['entity_id']);
    assertTrue(fathom()->api->latestResponse->successful());
});

test('report for an event', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response(['data' => []], 200),
    ]);

    $event = new Event('test', 'test');

    $request = fathom()->reports()->for($event)->aggregate(Aggregate::VISITS);
    $request->get();

    assertEquals(Entity::EVENT, $request->query()['entity']);
    assertEquals('test', $request->query()['entity_id']);
    assertTrue(fathom()->api->latestResponse->successful());
    assertTrue(fathom()->api->latestResponse->successful());
});

test('report for an arbitrary entity', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response(['data' => []], 200),
    ]);

    $request = fathom()->reports()->for('pageview', 'test')->aggregate(Aggregate::VISITS);
    $request->get();

    assertEquals(Entity::PAGEVIEW, $request->query()['entity']);
    assertEquals('test', $request->query()['entity_id']);
    assertTrue(fathom()->api->latestResponse->successful());
    assertTrue(fathom()->api->latestResponse->successful());
});

test('query builder', function () {
    $dateFrom = now()->startOfDay()->subDay();
    $dateTo = now()->startOfDay();

    $query = fathom()->reports()
        ->for('pageview', 'test')
        ->aggregate(['visits', 'uniques'])
        ->between($dateFrom, $dateTo)
        ->interval('hour')
        ->groupBy('hostname')
        ->timezone('UTC')
        ->orderBy('visits', true)
        ->where('device', '!=', 'iPhone')
        ->where('hostname', '<>', 'google.com')
        ->query();

    assertEquals('pageview', $query['entity']);
    assertEquals('visits,uniques', $query['aggregates']);
    assertEquals($dateFrom->getTimestamp(), $query['date_from']);
    assertEquals($dateTo->getTimestamp(), $query['date_to']);
    assertEquals('hour', $query['date_grouping']);
    assertEquals('hostname', $query['field_grouping']);
    assertEquals('UTC', $query['timezone']);
    assertEquals('visits:desc', $query['sort_by']);
    assertEquals([
        'property' => 'device',
        'operator' => 'is not',
        'value' => 'iPhone',
    ], $query['filters'][0]);
    assertEquals([
        'property' => 'hostname',
        'operator' => 'is not',
        'value' => 'google.com',
    ], $query['filters'][1]);
});

test('validate entity', function () {
    this()->expectException(MissingValueException::class);

    fathom()->reports()->aggregate(Aggregate::VISITS)->get();
});

test('validate entity id', function () {
    this()->expectException(MissingValueException::class);

    fathom()->reports()->aggregate(Aggregate::VISITS)->get('pageview');
});

test('validate aggregate', function () {
    this()->expectException(MissingValueException::class);

    fathom()->reports()->get(Entity::PAGEVIEW, 'test');
});

test('validate date grouping', function () {
    this()->expectException(IncorrectValueException::class);

    fathom()->reports()->interval('century');
});

test('validate field grouping', function () {
    this()->expectException(IncorrectValueException::class);

    fathom()->reports()->groupBy('id');
});

test('validate filter property', function () {
    this()->expectException(IncorrectValueException::class);

    fathom()->reports()->where('id', '=', '0');
});

test('validate filter operator', function () {
    this()->expectException(IncorrectValueException::class);

    fathom()->reports()->where(FilterProperty::REFERRER_HOSTNAME, '===', '');
});

test('validate sort field', function () {
    this()->expectException(IncorrectValueException::class);

    fathom()->reports()->orderBy('id');
});
