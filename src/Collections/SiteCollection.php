<?php

namespace Based\Fathom\Collections;

use Based\Fathom\Models\Site;
use Illuminate\Support\Collection;

/**
 * @method Site first(callable $callback = null, $default = null)
 * @method Site offsetGet($key)
 */
class SiteCollection extends Collection
{
    /** @var Site[] */
    protected $items = [];

    public function __construct(array $items)
    {
        $this->items = array_map(fn ($item) => new Site(...$item), $items);
    }
}
