<?php

namespace Based\Fathom\Commands;

use Illuminate\Console\Command;

class FathomCommand extends Command
{
    public $signature = 'fathom';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
