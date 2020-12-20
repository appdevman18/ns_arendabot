<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;

class FallbackController extends Controller
{
    /**
     * Respond with a generic message.
     *
     * @param  Botman  $bot
     * @return void
     */
    public function __invoke($bot): void
    {
        $bot->reply('Извините, я не понял эти команды. Попробуйте: /start или /menu');
    }
}
