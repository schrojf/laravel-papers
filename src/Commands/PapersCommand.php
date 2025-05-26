<?php

namespace Schrojf\Papers\Commands;

use Illuminate\Console\Command;
use Schrojf\Papers\Papers;

class PapersCommand extends Command
{
    public $signature = 'papers';

    public $description = 'List all registered papers';

    public function handle(): int
    {
        $this->comment('Listing registered papers...');

        $papers = Papers::all();

        $this->table(
            ['Paper Name', 'Url Key', 'Description'],
            array_map(fn (string $paper) => [$paper::name(), $paper::handler(), $paper::description()], $papers),
        );

        return self::SUCCESS;
    }
}
