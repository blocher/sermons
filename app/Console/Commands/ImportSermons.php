<?php

namespace App\Console\Commands;

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
            $this->info($parser->parse($file));
        }

    }

    protected function getFileList(): array
    {
        $files = Storage::allfiles();
        $files = array_filter($files, function ($item) {
            return strpos($item, '.docx') and strpos($item, 'ublic/sermons/');
        });
        return $files;
    }
}
