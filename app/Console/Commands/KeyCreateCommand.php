<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class That generates the app key
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class KeyCreateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'key:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a random string that is 32 characters long; you can use it as your app key.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Generated a 32 character long key');
        $this->line(md5(uniqid()));

    }
}
