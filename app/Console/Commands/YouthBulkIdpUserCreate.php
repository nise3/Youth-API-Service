<?php

namespace App\Console\Commands;

use App\Services\YouthManagementServices\YouthBulkImportFromOldSystemService;
use Illuminate\Console\Command;

class YouthBulkIdpUserCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youth:idp-user { --limit=100 : The number of users will be created }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create IDP user from imported youth from old system';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
       $limit=(int)$this->option('limit');
       if(is_numeric($limit)){
           app(YouthBulkImportFromOldSystemService::class)->createIdpUser($limit);
       }

    }
}
