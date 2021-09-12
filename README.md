# Fathom Analytics PHP API Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/based/fathom.svg?style=flat-square)](https://packagist.org/packages/based/fathom)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/lepikhinb/fathom-api/run-tests?label=tests)](https://github.com/lepikhinb/fathom-api/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/based/fathom.svg?style=flat-square)](https://packagist.org/packages/based/fathom)

This package is a wrapper for the newly released [Fathom Analytics](https://usefathom.com/) API. The package is in **wip** mode as the API is still in early access.

## Installation

This version supports PHP 8.0. You can install the package via composer:

```bash
composer require based/fathom
```

## Usage
### Account
```php
<?php

$fathom = new Fathom('token');

$fathom->account();
```

### Sites
```php
<?php

$fathom = new Fathom('token');

$fathom->sites()->get(
    limit: 10,          // A limit on the number of objects to be returned, between 1 and 100
    next: true          // Paginate requests
);

$site = $fathom->sites()->getSite(
    siteId: 'BASED',    // The ID of the site you wish to load
);

$fathom->sites()->create(
    name: 'purple-peak',
    sharing: 'private', // The sharing configuration. Supported values are: `none`, `private` or `public`. Default: `none`
    password: 'secret', // When sharing is set to private, you must also send a password to access the site with.
);

$fathom->sites()->update(
    siteId: 'BASED',
    name: 'purple-peak',
    sharing: Sharing::NONE,
    password: 'secret',
);

// Wipe all pageviews & event completions from a website
$fathom->sites()->wipe($siteId);

$fathom->sites()->delete($siteId);
```

### Events
```php
<?php

$fathom = new Fathom('token');

$fathom->events()->get(
    siteId: 'BASED',    // The ID of the site you wish to load events for
    limit: 10,          // A limit on the number of objects to be returned, between 1 and 100
    next: true          // Paginate requests
);

$fathom->events()->getEvent($siteId, $eventId);

$fathom->events()->create(
    siteId: 'purple-peak',
    name: 'Purchase early access',
);

$fathom->events()->update(
    siteId: 'BASED',
    eventId: 'purchase-early-access',
    name: 'Purchase early access (live)',
);

// Wipe all pageviews & event completions from a webevent
$fathom->events()->wipe($siteId, $eventId);

$fathom->events()->delete($siteId, $eventId);
```

### Aggregation
Generate an aggregation. This is effectively an unbelievably flexible report that allows you to group on any fields you wish, and filter them at your leisure.
```php
<?php

$fathom = new Fathom('token');

$fathom->reports()
    ->for('pageview', 'CNODFN')
    ->aggregate(['visits', 'uniques'])
    ->between(now()->subMonth()->startOfDay(), now())
    ->interval('hour')
    ->timezone('UTC')
    ->where('device', '!=', 'iPhone')
    ->where('hostname', '<>', 'google.com')
    ->groupBy('hostname')
    ->orderBy('visits', true)
    ->limit(10)
    ->get();

$fathom->reports()->for(Entity::PAGEVIEW, 'CNODFN');

// or
$site = $fathom->sites()->get()->first();
$fathom->reports($site)->get(
    aggregate: Aggregate::VISITS
);

// or
$site = $fathom->sites()->get()->first();
$fathom->reports()->get(
    entity: $site,
    aggregate: Aggregate::VISITS
);
```

### Get Current Visitors
```
$fathom->sites()->getCurrentVisitors('XXXXX');
```

## Laravel
This package contains a facade and a config file for Laravel applications.

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Based\Fathom\FathomServiceProvider" --tag="fathom-config"
```

This is the contents of the published config file:

```php
return [
    'token' => env('FATHOM_TOKEN'),
];
```

Update the config file directly, or set the environment variable `FATHOM_TOKEN` to your API key (*preferred*).

### Example
Example using a facade:
```php
<?php

use Based\Fathom\Facade\Fathom;

Fathom::account()->get();
Fathom::sites()->get();
Fathom::sites()->create(...);
```

Or create an instance directly:
```php
<?php

use Based\Fathom\Fathom;

$fathom = new Fathom(config('fathom.token'));
$fathom->account()->get();
```

## Testing

```bash
composer test
```

## Credits

- [Boris Lepikhin](https://github.com/lepikhinb)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
