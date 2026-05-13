<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunAutomatedBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-automated-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the automated backup for database and files.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automated backup...');

        Artisan::call('backup:run', [
            '--only-db' => true,
        ]);

        $this->info('Database backup completed.');

        Artisan::call('backup:run', [
            '--only-files' => true,
        ]);

        $this->info('Files backup completed.');
    }
}
