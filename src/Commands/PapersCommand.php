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
        $this->info('Listing registered papers...');

        $papers = Papers::all();

        if (empty($papers)) {
            $this->warn('No papers registered.');

            return self::SUCCESS;
        }

        $this->table(
            ['Paper Name', 'URL Key', 'Description'],
            array_map(
                fn (string $paper) => [
                    $paper::name(),
                    $paper::handler(),
                    $paper::description(),
                ],
                $papers
            ),
        );

        return self::SUCCESS;
    }
}
