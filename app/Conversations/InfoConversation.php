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

class InfoConversation extends Conversation
{
    public function askInfo()
    {
        $this->say(
            '<b>Аренда PS4 в Нур-Султане.</b>'.PHP_EOL.PHP_EOL.
            '💰 <b>Цены:</b>'.PHP_EOL.
            '1. В будние дни - 4000 тг'.PHP_EOL.
            '2. В выходные - 5000 тг'.PHP_EOL.
            '3. Доп. геймпад - 850 тг'.PHP_EOL.
            '4. Телевизор - 4000 тг'.PHP_EOL.PHP_EOL.

            '🎮 <b>Игры:</b>'.PHP_EOL.
            '1. FIFA 2021 | 20 | 19'.PHP_EOL.
            '2. Mortal Combat 11 | XL'.PHP_EOL.
            '3. UFC 3 | 4'.PHP_EOL.
            '4. NHL 2019 | 20'.PHP_EOL.
            '5. Grand Theft Auto V'.PHP_EOL.
            '6. Fortnite'.PHP_EOL.
            '7. Battlefield 1 | 3 | 4 | V'.PHP_EOL.
            '8. Need For Speed| Heat| Payback'.PHP_EOL.
            '9. Resident Evil 2'.PHP_EOL.
            '10. A Way Out'.PHP_EOL.
            '11. ...'.PHP_EOL.PHP_EOL.

            '⏲ Расчетное время (время возврата) 24 часа  с момента доставки.'.PHP_EOL.PHP_EOL.

            '🏠 На посуточные квартиры не сдаются, прошу не беспокоить по этому поводу. '.PHP_EOL.
            'На все PS4 нанесена гравировка и установлен GPS. '.PHP_EOL.
            'Все мошеннические виды деятельности, будут решаться через правоохранительные органы.'.PHP_EOL.PHP_EOL.

            '🚕 Бесплатная доставка и установка по городу.'.PHP_EOL.
            'В дальние районы, доставка оговаривается индивидуально.'.PHP_EOL.PHP_EOL.

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
        $this->askInfo();
    }

}
