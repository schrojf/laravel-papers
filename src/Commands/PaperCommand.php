<?php

namespace Schrojf\Papers\Commands;

use Illuminate\Console\Command;

class PaperCommand extends Command
{
    public $signature = 'laravel-papers';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
