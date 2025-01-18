<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdateProcessor;

class MyCustomCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:process'; // The command you will run in the terminal

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process the update tasks from the database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Initialize the UpdateProcessor and run the process method
        $updateProcessor = new UpdateProcessor();
        $updateProcessor->process();

        $this->info('Update tasks processed successfully!');
    }
}
