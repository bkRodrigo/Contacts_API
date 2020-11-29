<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Class That generates the app key
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class SQLiteDbCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'db:boot-tests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a sqlite database for the purpose of running tests.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Storage::put('database.sqlite', '');
    }
}
