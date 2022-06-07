<?php

namespace Bagoesz21\LaravelNotifWaWeb\Console\Commands;

use Illuminate\Console\Command;
use Bagoesz21\LaravelNotifWaWeb\WhatsappService;

class WhatsappSessionStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:session-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get whatsapp session status';

    /** @var \App\Services\Whatsapp\WhatsappService */
    protected $service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = WhatsappService::make();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $result = $this->service->statusSession();
        dump($result);
    }
}
