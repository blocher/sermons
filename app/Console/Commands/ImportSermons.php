<?php

namespace App\Console\Commands;

use App\Models\Sermon;
use App\Services\SermonParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportSermons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sermons:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import sermons from the Sermons folder';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $parser = new SermonParser();
        $file_list = $this->getFileList();
        foreach ($file_list as $file) {
            $item = $parser->parse($file);
            if (!$item) {
                $this->error($file);
            }
            $readings = implode('; ', $item['readings']);
            $sermon = Sermon::updateOrCreate(
                ['file_name' => $item['file_name']],
                ['delivered_on' => $item['date'], 'location' => $item['church'], 'feast_id' => $item['feast']->id, 'sermon_summary' => null, 'sermon_text' => $item['text'], 'sermon_markup' => $item['html'], 'file_name' => $item['file_name'], 'file' => $item['path'], 'readings' => $readings, 'title' => $item['title'], 'proper' => $item['proper'], 'mass_year' => $item['mass_year']]
            );
            print($sermon->id . ' ' . $sermon->file_name . PHP_EOL);
        }

    }

    protected function getFileList(): array
    {
        $files = Storage::allfiles();
        $files = array_filter($files, function ($item) {
            $not_temp_file = strpos($item, '/~') === false;
            $is_docx = strpos($item, '.docx') !== false;
            $in_sermon_folder = strpos($item, 'public/sermons/') !== false;
            $not_extraneous = true;
            $exclude_phrases = ['Reflections on the Passion', 'Jan 5-6', 'Advent Reopening Final', 'Advent Brookline 6.21.20 - Bulletin', 'Advent Sermon 3.7.21', "- Bulletin", "Good Shepherd 3.17.19.docx", "website", "8.10.14.docx", "printing copy", "2.20.11.docx"];
            foreach ($exclude_phrases as $phrase) {
                if (strpos($item, $phrase) !== false) {
                    $not_extraneous = false;
                }
            }
            return $not_temp_file && $is_docx && $in_sermon_folder && $not_extraneous;
        });
        return $files;
    }
}
