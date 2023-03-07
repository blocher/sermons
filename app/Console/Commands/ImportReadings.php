<?php

namespace App\Console\Commands;

use App\Models\Sermon;
use App\Utils\BibleGateway;
use App\Utils\BiblePassage;
use Exception;
use Illuminate\Console\Command;

class importReadings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sermons:importreadings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports readings associated with the sermon';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $sermons = Sermon::orderBy("delivered_on", "desc")->where("readings_string", "<>", "")->doesntHave('readings')->get();
        foreach ($sermons as $sermon) {
            print(PHP_EOL . $sermon->id . PHP_EOL);
            foreach ($sermon->readingsArray as $reading) {
                print($reading);
                $passage = new BiblePassage($reading);
                var_dump($passage);
                try {
                    $headings = implode('; ', array_map((fn($heading) => $heading['heading']), $passage->headings));
                } catch (Exception $e) {
                    print("Error: " . $reading . PHP_EOL);
                    $headings;
                }

                $sermon->readings()->firstOrCreate([
                    "passage" => $reading,
                ], [
                    "nrsv" => $passage->html,
                    "headings" => $headings,
                ]);
            }
        }
    }
}
