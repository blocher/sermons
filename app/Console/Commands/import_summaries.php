<?php

namespace App\Console\Commands;

use App\Models\Sermon;
use Exception;
use Illuminate\Console\Command;
use Orhanerday\OpenAi\OpenAi;

class import_summaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sermons:importsummaries';

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
        $sermons = Sermon::orderBy("delivered_on", "desc")->whereNull("sermon_summary")->get();
        foreach ($sermons as $sermon) {
            $open_ai_key = getenv('OPENAI_API_KEY');
            $open_ai = new OpenAi($open_ai_key);
            $complete = $open_ai->chat([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        "role" => "system",
                        "content" => "You are a research assistant with particular knowledge of the Bible and theology."
                    ],
                    [
                        "role" => "user",
                        "content" => "I wrote the following sermon; can you please briefly summarize it? " . $sermon->sermon_text
                    ],
                ],
                'temperature' => 1.0,
                'max_tokens' => 1000,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
            ]);
            $complete = json_decode($complete);
            try {
                var_dump($complete->choices);
            } catch (Exception $e) {
                continue;
            }

            $sermon->sermon_summary = $complete->choices[0]->message->content;
            $sermon->save();
            print($complete->choices[0]->message->content);
        }
    }
}
