<?php

namespace App\Console\Commands;

use App\Models\Location;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;


class ImportLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sermons:locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import locations data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        $csv = array_map("str_getcsv", file(Storage::path('locations.csv'), FILE_SKIP_EMPTY_LINES));
        $keys = array_shift($csv);
        foreach ($csv as $i => $row) {
            $csv[$i] = array_combine($keys, $row);
        }
        Location::truncate();
        var_dump($csv);
        foreach ($csv as $row) {
            Location::create($row);
        }
    }
}
