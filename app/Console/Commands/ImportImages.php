<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Models\Sermon;
use Buchin\GoogleImageGrabber\GoogleImageGrabber;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Orhanerday\OpenAi\OpenAi;

// write a method to get remote data from a url using file_get_contents and disabling ssl checks
function get_remote_data($url)
{
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

class ImportImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sermons:import_images';

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
        $sermons = Sermon::orderBy('delivered_on', 'desc')->get();
        foreach ($sermons as $sermon) {
            foreach ($sermon->readings as $reading) {
                $this->generateImageForSermon($sermon);


                if (in_array($reading->book, ["Matthew", "Mark", "Luke", "John"])) {
                    $keyword = $reading->headings;
                    print($keyword . PHP_EOL);
                    $num_images = 0;
                    $i = 0;
                    while (!$num_images and $i < 5) {
                        $images = array_slice(GoogleImageGrabber::grab($keyword), 0, 10);
                        $num_images = count($images);
                        if ($num_images == 0) {
                            sleep(5);
                        }
                        $i++;
                    }
                    $i = 0;
                    foreach ($images as $image) {
                        $file_name = $sermon->id . "-" . $i . "." . $image['filetype'];
                        $file_path = 'public/images/' . $file_name;
                        $contents = get_remote_data($image['url']);
                        Storage::put($file_path, $contents);
                        try {
                            $valid = exif_imagetype(Storage::path($file_path));
                        } catch (Exception $e) {
                            continue;
                        }
                        if ($valid) {
                            print($sermon->delivered_on . " " . $file_name . " " . $image['slug'] . PHP_EOL);
                            $res = Image::updateOrCreate(
                                ['original_url' => $image['url']],
                                [
                                    'keyword' => $image['keyword'],
                                    'slug' => $image['slug'],
                                    'title' => $image['title'],
                                    'alt' => $image['alt'],
                                    'filetype' => $image['filetype'],
                                    'width' => $image['width'],
                                    'height' => $image['height'],
                                    'source' => $image['source'],
                                    'domain' => $image['domain'],
                                    'original_thumbnail' => $image['thumbnail'],
                                    'filename' => $file_name,
                                    'file' => $file_path,
                                    'sermon_id' => $sermon->id,
                                ],
                            );
                            $res->filename = $file_name;
                            $res->file = $file_path;
                            $res->save();
                            $i++;
                        }
                    }
                    print(PHP_EOL . PHP_EOL);

                }
                continue;
            }
        }
    }

    public function generateImageForSermon($sermon): void
    {

        $open_ai_key = getenv('OPENAI_API_KEY');
        $open_ai = new OpenAi($open_ai_key);
        print('A' . PHP_EOL);
        $complete = $open_ai->chat([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    "role" => "system",
                    "content" => "You are a research assistant with particular knowledge of the Bible and theology, especially Anglican and Episcopal theology with an Anglo-Catholic flavor. You have knowledge of the Church Fathers."
                ],
                [
                    "role" => "user",
                    "content" => "I wrote the following sermon; can you write a one sentence prompt for an image (photograph or painting) that would illustrate it. " . $sermon->sermon_text
                ],
            ],
            'temperature' => 1.0,
            'max_tokens' => 1000,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);
        print('B' . PHP_EOL);
        $complete = json_decode($complete);
        print('C' . PHP_EOL);
        var_dump($complete);
        die();
        try {
            var_dump($complete->choices);
        } catch (Exception $e) {
            var_dump($e);
            die();
        }
        var_dump($complete);
        $prompt = $complete->choices[0]->message->content;
        var_dump($prompt);
        die();
        print($complete->choices[0]->message->content);

    }
}
