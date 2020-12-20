<?php

namespace App\Conversations;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class SaleConversation extends Conversation
{
    public function askSale()
    {
        $this->say(
            '<b>Акция PS4 2+1.</b>'.PHP_EOL.PHP_EOL.

            '⏲ Акция действует три дня недели (воскресенье, понедельник, вторник)'.PHP_EOL.PHP_EOL.

            '💰 Вы платите за 2 дня, и получаете +1 день бесплатной игры.'.PHP_EOL.
            'Получается 3 дня = за 8000 тг.'.PHP_EOL.
            'Сроки акции ограничены...'.PHP_EOL.PHP_EOL.

            '🎮 Наслаждайтесь игрой PS4'.PHP_EOL.PHP_EOL.

            '💬 <b><i>"Мы первое поколение людей, которое будет играть с детьми в видеоигры и понимать что, черт возьми, там происходит!"</i></b>'.PHP_EOL.PHP_EOL.

            'Звонить по телефону (whatsapp,telegram,#ps_arenda_ns):'.PHP_EOL.
            '☎ <b>8 708 742 35 99</b>',
            ['parse_mode' => 'HTML']
        );
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askSale();
    }

}
