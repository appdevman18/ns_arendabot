<?php

namespace App\Http\Controllers;

use App\Conversations\InfoConversation;
use App\Conversations\OrderConversation;
use App\Conversations\SaleConversation;
use App\Conversations\StartConversation;
use BotMan\BotMan\BotMan;

class BotManController extends Controller
{
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
     * @param  BotMan  $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new StartConversation());
    }

    public function infoConversation(BotMan $bot)
    {
        $bot->startConversation(new InfoConversation());
    }

    public function saleConversation(BotMan $bot)
    {
        $bot->startConversation(new SaleConversation());
    }

    public function orderConversation(BotMan $bot)
    {
        $bot->startConversation(new OrderConversation());
    }
}
