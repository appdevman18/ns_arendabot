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

class StartConversation extends Conversation
{
    protected $user;

    public function askStart()
    {
        $this->user = $this->getFullName();

        $this->say(
            '✋ Салам, <b>'.$this->user.'!</b>'.PHP_EOL.
            'Аренда 🎮 игровых приставок <b>PS4</b> в Нур-Султане. '.PHP_EOL.
            'Нажмите /menu для подробней информации.',
            ['parse_mode' => 'HTML']
        );
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askStart();
    }

    private function getFullName()
    {
        $user = $this->bot->getUser();
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();

        return $firstName.' '.$lastName;
    }
}
