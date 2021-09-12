<?php

namespace Based\Fathom\Models;

class CurrentVisitors
{
    public function __construct(
        public string $total,
        public array $content = []
    ) {}
}
