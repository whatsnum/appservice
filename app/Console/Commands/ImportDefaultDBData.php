<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class ImportDefaultDBData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:import_default_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import NumsApp Default Database Table Data';

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
      DB::unprepared(file_get_contents(base_path('database/defaults/content.sql')));
      DB::unprepared(file_get_contents(base_path('database/defaults/countries.sql')));
      DB::unprepared(file_get_contents(base_path('database/defaults/plan.sql')));
      DB::unprepared(file_get_contents(base_path('database/defaults/states.sql')));
      DB::unprepared(file_get_contents(base_path('database/defaults/interests.sql')));
      DB::unprepared(file_get_contents(base_path('database/defaults/job_titles.sql')));
    }
}
