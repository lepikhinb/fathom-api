<?php

namespace Based\Fathom\Collections;

use Based\Fathom\Models\Event;
use Illuminate\Support\Collection;

/**
 * @method Event first(callable $callback = null, $default = null)
 * @method Event offsetGet($key)
 */
class EventCollection extends Collection
{
    /** @var Event[] */
    protected $items = [];

    public function __construct(array $items)
    {
        $this->items = array_map(fn ($item) => new Event(...$item), $items);
    }
}
