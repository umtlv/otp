<?php

namespace Axel\Otp\Console;

use Illuminate\Console\Command;

class MigrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Otp migrations';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Receiving Otp Migrations...');

        $this->callSilent('vendor:publish', ['--tag' => 'otp-migrations']);

        $this->info('Otp migrations received successfully.');
    }
}