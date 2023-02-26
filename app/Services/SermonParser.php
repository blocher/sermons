<?php

namespace App\Services;

use App\Models\Day;
use App\Models\Holiday;
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
        $file_name = $this->getFileName($path);
        $date = $this->getDate($text, $file_name);

        $church = $this->getChurch($text, $file_name);
        $feast = $this->getFeast($date);
        $readings = $this->getReadings($text);
        $title = $this->getTitle($file_name);

        $text = trim(str_replace($this->getHeadings($text), "", $text));
        $html = trim(str_replace($this->getHeadings($html), "", $html));

        $proper = $this->getProper($date);
        $mass_year = $this->getMassYear($date);
        print($church);
        return [
            "path" => $path,
            "file_name" => $file_name,
            "readings" => $readings,
            "feast" => $feast,
            "proper" => $proper,
            "mass_year" => $mass_year,
            "church" => $church,
            "date" => $date,
            "text" => $text,
            "html" => $html,
            "title" => $title,
        ];
    }

    protected function getHTML($content): string
    {
        $htmlWriter = new HTML($content);
        $html = $htmlWriter->getContent();
        $html = $this->cleanHTML($html);
        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
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
        $clean_html = str_replace("\xc2", ' ', $clean_html);
        $clean_html = str_replace("\xa0", ' ', $clean_html);
        $clean_html = preg_replace('/[^\S\r\n]+/', ' ', $clean_html);
        $clean_html = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $clean_html);
        $clean_html = trim($clean_html);
        return $clean_html;
    }

    protected function getText($html): string
    {
        $text = strip_tags($html);
        $text = str_replace("\xc2", ' ', $text);
        $text = str_replace("\xa0", ' ', $text);
        $text = preg_replace('! +!', ' ', $text);
        $text = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $text);
        $text = trim($text);
        return $text;
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

    protected function getChurch($text, $filename): Location
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
                return $res;
            }
        }
        return "";
    }

    protected function getFeast($date): Holiday|null
    {
        $day = $this->getDay($date);
        $special_days = ["Christmas Eve", "Easter Eve", "Thanksgiving Day"];
        if ($day) {
            if ($day->holiday_2 && in_array($day->holiday_2, $special_days)) {
                return $day->holiday_2;
            }
            if ($day->holiday_3 && in_array($day->holiday_3, $special_days)) {
                return $day->holiday_3;
            }
            return $day->holiday_1;
        }
        return null;

//        $line = $lines[1];
//        $line = str_replace("—", "-", $line);
//        $line = str_replace("–", "-", $line);
//        $line = explode("-", $line);
//        if (count($line) > 1) {
//            return trim($line[1]);
//        }
//        return trim($line[0]);
    }

    protected function getDay($date): Day
    {
        return Day::where('date', $date)->first();
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

    protected function getTitle($filename): string
    {
        $filename = str_replace('.docx', '', $filename);
        $filename = preg_replace('/\d{1,2}.\d{1,2}.\d{2}/', "", $filename);
        $filename = preg_replace('/\s+/', ' ', $filename);
        $filename = trim($filename);
        $parts = explode('-', $filename);
        while (count($parts) > 1) {
            $last = array_pop($parts);
            if ($last) {
                return trim($last);
            }
        }
        return ucfirst($filename);
    }

    protected function getHeadings($text)
    {
        $lines = explode("\n", $text);
        $i = 0;
        foreach ($lines as $line) {
            $checks = [
                "St. David’s Episcopal Church",
                "Elizabeth Locher",
                "Grace Church, Alexandria, Virginia",
                "Judges 4:4-7; Psalm 123; 1 Thessalonians 5:1-11; Matthew 25:14-30",
                "Good Shepherd, Hazelwood",
                "Daniel 7:1-3,15-18, Psalm 149, Ephesians 1:11-23, Luke 6:20-31",
                "Christmas Day, 2013",
                "Zephaniah 3:14-20, Canticle 9, Philippians 4:4-7, Luke 3:7-18",
                "St. Paul’s Episcopal Church",
                "Preached at",
            ];
            $remove = false;
            foreach ($checks as $check) {
                if (strpos($line, $check) !== false) {
                    $remove = true;
                }
            }

            if (!$remove && strlen(trim($line)) > 60) {
                break;
            }
            if ($i > 10) {
                break;
            }
            $i++;
        }

        $headings = array_slice($lines, 0, $i);
        $headings = implode("\n", $headings);
        return $headings;
    }

    protected function getProper($date): int|null
    {
        $day = $this->getDay($date);
        return $day?->proper;
    }

    protected function getMassYear($date): string|null
    {
        $day = $this->getDay($date);
        if ($day) {
            return $day->mass_year;
        }
        return null;
    }

    protected function getLines($text): array
    {
        $lines = explode("\n", $text);
        $lines = array_values(array_filter($lines, function ($line) {
            return strlen(trim($line)) > 0;
        }));
        return $lines;
    }

}

