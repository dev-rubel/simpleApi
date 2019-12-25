<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Wildcard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wild:card';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migration & Table seed & Server run in one commend.';

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
     *
     * @return mixed
     */
    public function handle()
    {
        // create table 
        if (!Schema::hasTable('people')) {
            Artisan::call('migrate', array('--path' => 'database/migrations', '--force' => true));
        }
        // create table seed
        if (Schema::hasTable('people')) {
            if (DB::table('people')->count() == 0) {
                Artisan::call('db:seed');
            }
        }
        Artisan::call('serve --port=8080');
        echo 'Server run....';
    }
}
