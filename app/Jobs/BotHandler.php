<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Bot\Bot;
use App\Bot\Chat;

class BotHandler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messaging;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->messaging->getType() == "message") {
            $bot = new Bot($this->messaging);
            $custom = $bot->extractDataFromMessage();
            //a request for a new question
            if ($custom["type"] == Chat::$NEW_QUESTION) {
                $bot->reply(Chat::getNew($custom['user_id']));
            } else if ($custom["type"] == Chat::$ANSWER) {
                $bot->reply(Chat::checkAnswer($custom["data"]["answer"], $custom['user_id']));
            } else {
                $bot->reply("I don't understand. Try \"new\" for a new question");
            }
        }
    }
}
