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
            var_dump($item);
            $sermon = Sermon::updateOrCreate(
                ['file_name' => $item['file_name']],
                ['delivered_on' => $item['date'], 'location' => $item['church'], 'feast' => $item['feast'], 'sermon_summary' => null, 'sermon_text' => $item['text'], 'sermon_markup' => $item['html'], 'file_name' => $item['file_name'], 'file' => $item['path'], 'readings' => $readings]
            );
            print($sermon->id);
        }

    }

    protected function getFileList(): array
    {
        $files = Storage::allfiles();
        $files = array_filter($files, function ($item) {

            return strpos($item, '/~') === false && strpos($item, '.docx') && strpos($item, 'ublic/sermons/');
        });
        return $files;
    }
}
