<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

use App\Imports\CircuitImport;
use Maatwebsite\Excel\Facades\Excel;

class uploadVolunteer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'circuits:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload the circuits through an excel file';

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
     * @return int
     */
    public function handle()
    {
        
        Excel::import(new CircuitImport, Storage::disk('local')->path('/excel/estados/amazonas.xlsx'));
        $this->info("Updated circuits");

        return 0;
    }
}
