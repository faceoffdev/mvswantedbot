<?php

namespace App\Http\Controllers;

use App\Conversations\SearchConversation;
use BotMan\BotMan\BotMan;

class BotManController extends Controller
{
    public function handle(): void
    {
        /** @var \BotMan $botman */
        $botman = app('botman');

        $botman->listen();
    }

    public function searchConversation(BotMan $bot): void
    {
        $bot->startConversation(new SearchConversation());
    }
}
