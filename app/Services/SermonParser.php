<?php

namespace App\Services;

use App\Models\Location;
use App\Utils\BibleVerseServiceExtended as BibleVerseService;
use App\Utils\FindDate;
use Carbon\Carbon;
use Carbon\Exceptions\OutOfRangeException;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\HTML;
use TypeError;
use ValueError;

class SermonParser
{

    public function parse($path): array
    {
        $this->path = $path;
        try {
            $phpWord = IOFactory::load(Storage::path($path));
        } catch (ValueError $e) {
            return false;
        }
        $html = $this->getHTML($phpWord);
        $text = $this->getText($html);
        $lines = $this->getLines($text);
        $file_name = $this->getFileName($path);
        $date = $this->getDate($text, $file_name);

        $church = $this->getChurch($text, $file_name);
        $feast = $this->getFeast($lines);
        $readings = $this->getReadings($text);

        return [
            "path" => $path,
            "file_name" => $file_name,
            "readings" => $readings,
            "feast" => $feast,
            "church" => $church,
            "date" => $date,
            "text" => $text,
            "html" => $html,
        ];
    }

    protected function getHTML($content): string
    {
        $htmlWriter = new HTML($content);
        $html = $htmlWriter->getContent();
        $html = $this->cleanHTML($html);
        return $html;
    }

    protected function cleanHTML($html): string
    {
        $html = str_replace("> </span>", ">&nbsp;</span>", $html);
        $html = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $html);
        $config = HTMLPurifier_Config::createDefault();
        $config->set("CSS.AllowedProperties", ["text-decoration", "font-weight", "font-style"]);
        $config->set('AutoFormat.RemoveEmpty', true); // remove empty tag pairs
        $config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true); // remove empty, even if it contains an &nbsp;
        $config->set('AutoFormat.RemoveEmpty.RemoveNbsp.Exceptions', ["span"]);
        $config->set('AutoFormat.RemoveSpansWithoutAttributes', true);
        $config->set('AutoFormat.AutoParagraph', true); // remove empty tag pairs
        $purifier = new HTMLPurifier($config);
        $clean_html = $purifier->purify($html);
        return $clean_html;
    }

    protected function getText($html): string
    {
        $text = strip_tags($html);
        $text = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $text);
        return $text;
    }

    protected function getLines($text): array
    {
        $lines = explode("\n", $text);
        $lines = array_values(array_filter($lines, function ($line) {
            return strlen(trim($line)) > 0;
        }));
        return $lines;
    }

    protected function getFileName($path): string
    {
        $file_name = explode('/', $path);
        $file_name = end($file_name);
        return $file_name;

    }

    protected function getDate($text, $file_name): Carbon|false
    {
        preg_match('/\d{1,2}.\d{1,2}.\d{2}/', $file_name, $output_array);
        if (count($output_array) > 0) {
            $match = $output_array[0];
            $match = str_replace(['-', ' '], '.', $match);
            $date = Carbon::createFromFormat('m.d.y', $match);
            $date->hour = 0;
            $date->minute = 0;
            $date->second = 0;
            return $date;
        }
        try {
            $date = FindDate::findDate($text);
        } catch (OutOfRangeException $e) {
            return false;
        } catch (TypeError $e) {
            return false;
        }
        $date->hour = 0;
        $date->minute = 0;
        $date->second = 0;
        return $date;
    }

    protected function getChurch($text, $filename): string
    {
        $csv = array_map("str_getcsv", file(Storage::path('locationmappings.csv'), FILE_SKIP_EMPTY_LINES));
        $keys = array_shift($csv);
        foreach ($csv as $i => $row) {
            $csv[$i] = array_combine($keys, $row);
        }
        foreach ($csv as $mapping) {
            if (strpos($text, $mapping['needle']) !== false || strpos($filename, $mapping['needle']) !== false) {
                if (trim($mapping['city']) != "") {
                    $res = Location::where('name', $mapping['name'])->where('city', $mapping['city'])->first();
                } else {
                    $res = Location::where('name', $mapping['name'])->first();
                }
                if (!$res) {
                    var_dump($mapping, "POOP!");
                    die();
                }
                return $res->name;
            }
        }
        return "";
    }

    protected function getFeast($lines): string
    {
        $line = $lines[1];
        $line = str_replace("—", "-", $line);
        $line = str_replace("–", "-", $line);
        $line = explode("-", $line);
        if (count($line) > 1) {
            return trim($line[1]);
        }
        return trim($line[0]);
    }

    public function getReadings($text): array
    {
        $service = new BibleVerseService();
        $verses = $service->stringToBibleVerse($text);
        $grouped_verses = [];
        foreach ($verses as $verse) {
            $grouped_verses[$verse->getFromBookId()][] = $verse;
        }
        $result = [];
        foreach ($grouped_verses as $group) {
            if (count($group) == 1) {
                $result[] = $service->bibleVerseToString($group[0], lang: "en");
            } else {
                $result[] = $service->nonContiguousVersesToString($group, lang: "en");
            }

        }
        return $result;
    }

//    protected function getCombinedString($a, $b)
//    {
//        if ($a->getFromBookId() != $b->getFromBookId()) {
//            return false;
//        }
//        if ($a->getToChaper() == $b->getFromChapter()) {
//            $book = $this->bibleData[$bibleVerse->getFromBookId()] ["desc"] [$lang] ["long"]
//        }
//
//
//    }


}

