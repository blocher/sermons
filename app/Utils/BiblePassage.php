<?php

namespace App\Utils;

use Illuminate\Support\Facades\Http;

/**
 *
 */
class BiblePassage
{
    public $passage;
    public $text;
    public $version;

    protected $base_url = "https://api.dailyoffice2019.com/api/v1/bible/";

    public function __construct($passage, $version = 'NRSVCE')
    {
        $this->passage = $passage;
        $version = strtoupper($version);
        if ($version == "NRSV") {
            $version = "NRSVCE";
        }
        $this->version = $version;
        $url = $this->base_url . urlencode($passage) . "/" . $version;
        $response = Http::get($url)->json();
        print($url);
        if (!$response) {
            print("ERRRRROR" . PHP_EOL);
            return;
        }
        print(PHP_EOL);
        foreach ($response as $key => $value) {
            $this->$key = $value;
        }
    }
}

{

}
