<?php

namespace App\Http\Controllers;

use App\Conversations\RegisterConversation;
use App\Repositories\CityRepository;
use App\Repositories\UserRepository;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\ExampleConversation;
use App\Conversations\TestConversation;

class BotManController extends Controller
{

    protected $city;

    public function __construct()
    {
        $this->city = app(CityRepository::class);
        $this->user = app(UserRepository::class);
    }

    /**
     * Place your BotMan logic here.
     */

    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }

    public function testConversation(BotMan $bot)
    {
        $bot->startConversation(new TestConversation());
    }

    public function settingsConversation(BotMan $bot)
    {
        $bot->startConversation(new RegisterConversation());
    }
}
