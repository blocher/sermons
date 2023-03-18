<?php

namespace App\Console\Commands;

use App\Models\Reading;
use App\Utils\BibleVerseServiceExtended;
use Illuminate\Console\Command;

class ExpandPassages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sermons:expand_passages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $readings = Reading::orderBy("created", "desc")->get();
        foreach ($readings as $reading) {
            $service = new BibleVerseServiceExtended();
            $found = $service->stringToBibleVerse($reading->passage);
            if (!$found) {
                print("Not found: " . $reading->passage . PHP_EOL);
                continue;
            }
            $book = $service->getBibleData()[$found[0]->getBookId()]["desc"] ["en"] ["long"];
            $from_chapter = 0;
            $from_verse = 0;
            $to_chapter = 0;
            $to_verse = 0;
            foreach ($found as $item) {
                if (!$from_chapter or $item->getFromChapter() < $from_chapter) {
                    $from_chapter = $item->getFromChapter();
                    $from_verse = $item->getFromVerse();
                } elseif ($item->getFromChapter() == $from_chapter && $item->getFromVerse() < $from_verse) {
                    $from_verse = $item->getFromVerse();
                }
                if ($item->getToChapter() > $to_chapter) {
                    $to_chapter = $item->getToChapter();
                    $to_verse = $item->getToVerse();
                } elseif ($item->getToChapter() == $to_chapter && $item->getToVerse() > $to_verse) {
                    $to_verse = $item->getToVerse();
                }

            }
            print($from_chapter . ":" . $from_verse . " - " . $to_chapter . ":" . $to_verse);
            $reading->start_chapter = $from_chapter;
            $reading->start_verse = $from_verse;
            $reading->end_chapter = $to_chapter;
            $reading->end_verse = $to_verse;
            $reading->book = $book;
            $reading->save();
        }


    }
}
