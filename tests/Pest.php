<?php

use Based\Fathom\Fathom;
use Illuminate\Http\Client\Factory;
use Pest\TestSuite;
use PHPUnit\Framework\TestCase;

function this(): TestCase
{
    return TestSuite::getInstance()->test;
}

function fathom(): Fathom
{
    return this()->fathom ??= new Fathom('test-token');
}

function httpClient(): Factory
{
    return fathom()->api->httpClient;
}

function sitesDataset()
{
    return [
        [
            'id' => 'CDBUGS',
            'object' => 'site',
            'name' => 'Bugs Bunny Portfolio',
            'sharing' => 'none',
            'created_at' => '2020-07-27 12:01:01'
        ],
        [
            'id' => 'GCDFS',
            'object' => 'site',
            'name' => 'Acme Holdings Inc',
            'sharing' => 'private',
            'created_at' => '2020-07-27 12:01:01'
        ],
    ];
}

function eventsDataset()
{
    return [
        [
            'id' => 'purchase-early-access',
            'object' => 'event',
            'name' => 'Purchase early access',
            'created_at' => '2020-07-27 12:01:01'
        ],
        [
            'id' => 'purchase-early-access',
            'object' => 'event',
            'name' => 'Purchase early access (live)',
            'created_at' => '2020-07-27 12:01:01'
        ],
    ];
}

function accountDataset()
{
    return [
        'id' => 1,
        'object' => 'account',
        'name' => 'boris',
        'email' => 'boris@reel.so',
    ];
}
