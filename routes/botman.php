<?php

use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('/(start|search)', BotManController::class.'@searchConversation');

