<?php
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');
$botman->hears('Ask', BotManController::class.'@testConversation');
$botman->hears('/start', BotManController::class.'@settingsConversation');
$botman->fallback('App\Http\Controllers\FallbackController@index');
