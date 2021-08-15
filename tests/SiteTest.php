<?php

use Based\Fathom\Collections\SiteCollection;
use Based\Fathom\Models\Site;
use Illuminate\Http\Client\Request;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertTrue;

test('get site', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response(sitesDataset()[0], 200),
    ]);

    $site = fathom()->sites()->getSite('CDBUGS');

    assertInstanceOf(Site::class, $site);
    assertEquals('CDBUGS', $site->id);
});

test('get sites', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response([
            'data' => sitesDataset(),
        ], 200),
    ]);

    $sites = fathom()->sites()->get();

    assertInstanceOf(SiteCollection::class, $sites);
    assertInstanceOf(Site::class, $sites->first());
});

test('pagination', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => function (Request $request) {
            return httpClient()->response([
                'data' => [$request->data()['starting_after'] ? sitesDataset()[0] : sitesDataset()[1]],
            ], 200);
        },
    ]);

    $x = fathom()->sites()->get(1);

    $site1 = fathom()->sites()->get(1)->first();
    $site2 = fathom()->sites()->get(1, true)->first();

    assertNotEquals($site2->id, $site1->id);
});

test('update site', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response(sitesDataset()[0], 200),
    ]);

    $site = fathom()->sites()->update('CDBUGS', 'name', 'none');

    assertInstanceOf(Site::class, $site);
    assertEquals('CDBUGS', $site->id);
});

test('delete site', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response([], 200),
    ]);

    fathom()->sites()->delete('CDBUGS');

    assertTrue(fathom()->api->latestResponse->successful());
});

test('wipe site', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response([], 200),
    ]);

    fathom()->sites()->wipe('CDBUGS');

    assertTrue(fathom()->api->latestResponse->successful());
});
