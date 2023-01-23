<?php

namespace Axel\Otp\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Otp resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Publishing Otp Configurations...');

        $this->callSilent('vendor:publish', ['--tag' => 'otp-config']);

        $this->info('Otp installed successfully.');
    }
}