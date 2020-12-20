<?php

use App\Http\Controllers\BotManController;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

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

$botman->hears(
    'php artisan cache:clear',
    function ($bot) {
        $bot->reply('Кеш очищен');

        Log::debug('CLEARED');
        Artisan::call('cache:clear');
    }
);

