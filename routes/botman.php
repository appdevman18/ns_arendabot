<?php

use App\Http\Controllers\BotManController;
use App\Http\Controllers\Menu\Main;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use Carbon\Carbon;
use Illuminate\Support\Collection;

use Location\Coordinate;
use Location\Distance\Haversine;
use Location\Distance\Vincenty;
use Location\Line;


$botman = resolve('botman');

$botman->hears('/start', BotManController::class.'@startConversation');

$botman->hears(
    '/menu',
    function ($bot) {
        $bot->reply(
            'Главное меню',
            Keyboard::create()
                ->type(Keyboard::TYPE_KEYBOARD)
                ->oneTimeKeyboard()
                ->resizeKeyboard()
                ->addRow(KeyboardButton::create('ℹ Инфо')->callbackData('info'))
                ->addRow(KeyboardButton::create('🎟 Заявка')->callbackData('order'))
                ->addRow(KeyboardButton::create('🎁 Акции')->callbackData('sale'))
                ->toArray()
        );
    }
);

$botman->hears('ℹ Инфо', BotManController::class.'@infoConversation');
$botman->hears('🎟 Заявка', BotManController::class.'@orderConversation');
$botman->hears('🎁 Акции', BotManController::class.'@saleConversation');

$botman->fallback('App\Http\Controllers\FallbackController');
