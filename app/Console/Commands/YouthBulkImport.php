<?php

namespace App\Console\Commands;

use App\Services\YouthManagementServices\YouthBulkImportFromOldSystemService;
use Illuminate\Console\Command;

class YouthBulkImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youth:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Youth import from old system';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        app(YouthBulkImportFromOldSystemService::class)->youthBulkImportFromOldSystem();
    }
}
