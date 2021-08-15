<?php

use Based\Fathom\Models\Account;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;

test('get account', function () {
    httpClient()->fake([
        'https://api.usefathom.com/v1/*' => httpClient()->response(accountDataset(), 200),
    ]);

    $account = fathom()->account()->get();

    assertInstanceOf(Account::class, $account);
    assertEquals(1, $account->id);
});
