<?php

namespace App\Console\Commands;

use App\services\UpdateProcessor;
use Illuminate\Console\Command;

class ProcessUpdateTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-update-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Initialize the UpdateProcessor and run the process method
        $updateProcessor = new UpdateProcessor();
        $updateProcessor->process();

        $this->info('Update tasks processed successfully!');
    }
}
