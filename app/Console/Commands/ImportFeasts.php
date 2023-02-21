<?php

namespace App\Console\Commands;

use App\Models\Day;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Goutte\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;

function advent(int $year): Carbon
{
    $date = Carbon::now()->setYear($year)->setMonth(12)->setDay(25);
    if ($date->dayOfWeek == Carbon::SUNDAY) {
        $date->subWeeks(4);
    } else {
        $date->subWeeks(3);
    }
    $date->startOfWeek(Carbon::SUNDAY);
    return $date;
}

function getLiturgicalYear($date)
{
    $year = $date->year;
    $advent = advent($year);
    // If the date is before Advent, subtract one year
    if ($date->lessThan($advent)) {
        $year--;
    }
    // Calculate the liturgical year based on modulo operation
    switch ($year % 3) {
        case 0:
            return "A";
        case 1:
            return "B";
        case 2:
            return "C";
    }
}

function getOfficeYear($date)
{
    $year = $date->year;
    $advent = advent($year);
    // If the date is before Advent, subtract one year
    if ($date->lessThan($advent)) {
        $year--;
    }
    // Calculate the liturgical year based on modulo operation
    if ($year % 2 == 0) {
        return 1;
    }
    return 2;
}

function nameToFeast($name)
{
    $split_name = trim(explode("(", $name)[0]);
    $feast = Holiday::where('name', $split_name)->first();
    if (!$feast) {
        print($split_name);
    }
    return $feast;
}


function feastToProper($feast, $date)
{
    if (!$feast) {
        return null;
    }

    $date = $date->clone();
    $proper_data = [
        '1,5,11',
        '2,5,18',
        '3,5,25',
        '4,6,1',
        '5,6,8',
        '6,6,15',
        '7,6,22',
        '8,6,29',
        '9,7,6',
        '10,7,13',
        '11,7,20',
        '12,7,27',
        '13,8,3',
        '14,8,10',
        '15,8,17',
        '16,8,24',
        '17,8,31',
        '18,9,7',
        '19,9,14',
        '20,9,21',
        '21,9,28',
        '22,10,5',
        '23,10,12',
        '24,10,19',
        '25,10,26',
        '26,11,2',
        '27,11,9',
        '28,11,16',
        '29,11,23',
    ];

    $isAfterPentecost = strpos($feast->handle, "AfterPentecost") !== false;
    if (!$isAfterPentecost) {
        return null;
    }

    $date->setHour(0)->setMinute(0)->setSecond(0);

    while ($date->dayOfWeek != Carbon::SUNDAY) {
        $date->subDay();
    }

    $propers = [];
    foreach ($proper_data as $proper) {
        $parts = explode(",", $proper);
        $propers[$parts[0]] = Carbon::createMidnightDate($date->year, $parts[1], $parts[2], "America/New_York");
    }
    foreach ($propers as $proper => $proper_date) {
        $diff = $date->diffInDays($proper_date);
        if ($diff >= -3 && $diff <= 3) {
            return $proper;
        }
    }
    return null;
}

class ImportFeasts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sermons:importfeasts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Feasts';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (1 == 1) {
            $response = Http::get('https://liturgical-calendar.com/GetCalendar.php?Calendar=Episcopal');
            Holiday::truncate();
            foreach ($response->json()['Holidays'] as $holiday) {
                Holiday::create([
                    'handle' => $holiday['Handle'],
                    'name' => $holiday['Name']['en'],
                    'rank' => $holiday['Style'],
                    'priority' => $holiday['Priority'],
                    'color' => array_key_exists('Colour', $holiday) && $holiday['Colour'] ? $holiday['Colour'] : "White",
                ]);
                $this->info($holiday['Name']['en']);
            }
        }

        $period = CarbonPeriod::create('2010-01-01', '2030-12-31');

        // Iterate over the period
        foreach ($period as $date) {
            print("***" . $date . PHP_EOL);
            $retry_client = new RetryableHttpClient(HttpClient::create());
            $client = new Client($retry_client);
            $crawler = $client->request('GET', 'https://liturgical-calendar.com/en/Episcopal/' . $date->format('Y-m-d'));
            $i = 1;
            $crawler->filter('h2')->each(function ($node) use (&$i, $date) {
                $column = 'holiday_' . $i . '_id';
                $column_str = 'holiday_' . $i;
                $feast = nameToFeast($node->text());
                $proper = feastToProper($feast, $date);
                Day::updateOrCreate(
                    ['date' => $date],
                    [$column => $feast ? $feast->id : '', $column_str => $feast ? $feast->name : '', "mass_year" => getLiturgicalYear($date), "office_year" => getOfficeYear($date), "proper" => $proper]
                );
                $i++;
            });
        }
    }
}
