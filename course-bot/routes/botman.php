<?php 
$botman = resolve('botman');
use App\Http\Controllers\BotManController;
$botman->hears('Hi', function ($bot) {
    $loggedIn = true;
    $bot->reply('Hello!');
    if($loggedIn)
    $bot->startConversation(new App\Conversations\preLoginConversation);
    else
    $bot->startConversation(new App\Conversations\postLoginConversation);
}); 

$botman->hears('Start conversation', BotManController::class.'@startConversation');