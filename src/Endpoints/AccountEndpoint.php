<?php

namespace Based\Fathom\Endpoints;

use Based\Fathom\Api;
use Based\Fathom\Models\Account;

class AccountEndpoint
{
    public function __construct(
        protected Api $api
    ) {
    }

    /**
     * Retrieve information about the account that owns the API key.
     * 
     * @return \Based\Fathom\Models\Account 
     * 
     * @throws \Based\Fathom\Exceptions\AuthenticationException 
     * @throws \Exception 
     */
    public function get()
    {
        return new Account(
            ...$this->api->get('account')->json()
        );
    }
}
