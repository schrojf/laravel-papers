<?php

namespace Schrojf\Papers\Commands;

use Illuminate\Console\Command;

class PapersCommand extends Command
{
    public $signature = 'papers';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
