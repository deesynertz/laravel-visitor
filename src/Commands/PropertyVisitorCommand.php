<?php

namespace Deesynertz\Visitor\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Deesynertz\Visitor\Services\VisitorService;


class PropertyVisitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visitor:action 
            {name : The name of the action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Contain cronjob commants';


    protected $visitorService;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->visitorService = (new VisitorService);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // dd($this->argument('name'));

        // # Run the individual commands
        if ($this->argument('name') == 'auto-signout-visitor') {
            $this->visitorService->autoSignoutVisitor();
        }

        # Log a message
        $this->info('visitor:action has been excuted');
    }
}
