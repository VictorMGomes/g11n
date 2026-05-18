<?php

declare(strict_types=1);

namespace Victormgomes\G11n\Commands;

use Illuminate\Console\Command;

class G11nCommand extends Command
{
    public $signature = 'g11n';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
