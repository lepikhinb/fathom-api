<?php

namespace Based\Fathom\Models;

class Account
{
    public function __construct(
        public int $id,
        public string $object,
        public string $name,
        public string $email
    ) {
    }
}
