<?php

namespace Based\Fathom\Models;

use Carbon\Carbon;

class Event
{
    public ?Carbon $created_at;

    public function __construct(
        public string $id,
        public string $name,
        public ?string $site_id = null,
        public ?string $object = null,
        string $created_at = null,
    ) {
        $this->created_at = $created_at ? Carbon::parse($created_at) : null;
    }
}
