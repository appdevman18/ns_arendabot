<?php

namespace App\Conversations;

use App\Costumer;
use App\Order;
use App\Location as OrderLocation;
use BotMan\BotMan\Messages\Attachments\Contact;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Collection;
use Location\Coordinate;
use Location\Distance\Vincenty;

class OrderConversation extends Conversation
{
    protected $fullName, $user, $game, $day, $type, $contact, $location, $status = 0, $address, $distance;
    protected $priceForDistance = ['first' => 0, 'second' => 1000, 'third' => 2000];
    protected $distanceForPrice = ['first' => 5000, 'second' => 12000, 'third' => 20000];
    protected $locationUs = ['latitude' => 51.115451, 'longitude' => 71.439056];
    protected $priceUs = 4000;
    protected $dontDelivery = 0;
    protected $delivery = [
        'delivery' => 'С доставкой',
        'pickup' => 'Самовывоз',
        'no_delivery' => 'Извините в ваш регион не можем доставить приставку'
    ];

    /**
     * @return OrderConversation
     */
    public function askOrder()
    {
        $this->fullName = $this->getFullName();
        $this->user = $this->bot->getUser();

        $question = Question::create('Как хотите заказать приставку? ')
            ->fallback('Невозможно задать вопрос')
            ->callbackId('ask_order')
            ->addButtons(
                [
                    Button::create('🚗 С доставкой')->value('delivery'),
                    Button::create('🔛 Самовывоз')->value('pickup'),
                ]
            );

        return $this->ask(
            $question,
            function (Answer $answer) {
                if ($answer->isInteractiveMessageReply()) {
                    if ($answer->getValue() === 'delivery') {
                        $this->say('<b><i>Доставка</i></b>.', ['parse_mode' => 'HTML']);
                        $this->type = 2;
                        $this->askGame();
                    } else {
                        $this->say('<b><i>Самовывоз</i></b>.', ['parse_mode' => 'HTML']);
                        $this->type = 1;
                        $this->askGame();
                    }
                }
            }
        );
    }

    /**
     * @return OrderConversation
     */
    public function askGame()
    {
        $question = Question::create('Выберите основную игру')
            ->fallback('Невозможно задать вопрос')
            ->callbackId('ask_game')
            ->addButton(Button::create('FIFA 21')->value('FIFA 21'))
            ->addButton(Button::create('GTA V')->value('GTA V'))
            ->addButton(Button::create('Need For Speed')->value('Need For Speed'))
            ->addButton(Button::create('BattleField')->value('BattleField'))
            ->addButton(Button::create('Mortal Kombat')->value('Mortal Kombat'))
            ->addButton(Button::create('UFC')->value('UFC'));

        return $this->ask(
            $question,
            function (Answer $answer) {
                if ($answer->isInteractiveMessageReply()) {
                    $this->game = $answer->getValue();
                    $this->say('<b><i>'.$this->game.'</i></b>', ['parse_mode' => 'HTML']);
                    $this->askDay();
                }
            }
        );
    }

    /**
     * @return OrderConversation
     */
    public function askDay()
    {
        $question = Question::create('На сколько дней хотите арендовать?')
            ->fallback('Невозможно задать вопрос')
            ->callbackId('ask_date')
            ->addButton(Button::create('1 день')->value('1 день'))
            ->addButton(Button::create('2 дня')->value('2 дня'))
            ->addButton(Button::create('3 дня')->value('3 дня'))
            ->addButton(Button::create('4 дня')->value('4 дня'))
            ->addButton(Button::create('5 дней')->value('5 дней'))
            ->addButton(Button::create('6 дней')->value('6 дней'))
            ->addButton(Button::create('7 дней')->value('7 дней'));

        return $this->ask(
            $question,
            function (Answer $answer) {
                if ($answer->isInteractiveMessageReply()) {
                    $this->day = $answer->getValue();
                    $this->say('<b><i>'.$this->day.'</i></b>', ['parse_mode' => 'HTML']);
                    $this->askLocation();
                }
            }
        );
    }

    public function askLocation()
    {
        $this->askForLocation(
            'Отправьте адрес доставки.',
            function (Location $location) {
                $this->say('Спасибо. Ваш адрес принят.');

                $this->location = $location;

                $this->distance = $this->getDistanceForPrice(
                    $this->location->getLatitude(),
                    $this->location->getLongitude()
                );

                if ($this->distance > $this->distanceForPrice['third']) {
                    $this->repeat($this->delivery['no_delivery']);
                } else {
                    $this->askContact();
                }
            },
            null,
            $this->keyboardLocation()
        );
    }

    public function askContact()
    {
        $this->askForContact(
            'Отправьте номер телефона.',
            function (Contact $contact) {
                $this->contact = $contact;

                $attachment = new Location(
                    $this->location->getLatitude(),
                    $this->location->getLongitude(),
                    [
                        'custom_payload' => true,
                    ]
                );
                $this->address = OutgoingMessage::create('Ваш отправленный адрес доставки')
                    ->withAttachment($attachment);

                $this->say(
                    'Спасибо. Ваш номер принят.'.PHP_EOL.PHP_EOL.
                    '<b>Проверьте данные, если все правильно можете потвердить ваш заказ.</b>'.PHP_EOL.PHP_EOL.
                    '<b>Ваше имя: </b><i>'.$this->fullName.'</i>'.PHP_EOL.
                    '<b>Тип аренды: </b><i>'.$this->isDelivery().'</i>'.PHP_EOL.
                    '<b>Основная игра: </b><i>'.$this->game.'</i>'.PHP_EOL.
                    '<b>Срок аренды: </b><i>'.$this->day.'</i>'.PHP_EOL.
                    '<b>Цена (тенге): </b><i>'.$this->getPrice().'</i>'.PHP_EOL.
                    '<b>Ваш номер: </b><i>'.$this->contact->getPhoneNumber().'</i>'.PHP_EOL.PHP_EOL,
                    ['parse_mode' => 'HTML']
                );
                $this->bot->reply($this->address);
                $this->askConfirm();
            },
            null,
            $this->keyboardContact()
        );
    }

    public function askConfirm()
    {
        $question = Question::create('Вы потверждаете заказ?')
            ->fallback('Невозможно задать вопрос')
            ->callbackId('ask_confirm')
            ->addButton(Button::create('Да')->value('yes'))
            ->addButton(Button::create('Нет')->value('no'));

        return $this->ask(
            $question,
            function (Answer $answer) {
                if ($answer->isInteractiveMessageReply()) {
                    $answer = $answer->getValue();
                    if ($answer === 'yes') {
                        $this->status = 1;

                        $this->say(
                            'Спасибо, <b>'.$this->fullName.'</b> ваш заказ принят.'.PHP_EOL.
                            'Администратор свяжется с вами через несколько минут...'.PHP_EOL,
                            [
                                'parse_mode' => 'HTML',
                                'reply_markup' => json_encode(
                                    Collection::make(['remove_keyboard' => true,])->filter()
                                )
                            ]
                        );
                        $this->sendOrderToManager('1474642083');
                        $this->createOrder();
                    } else {
                        $this->status = 0;

                        $this->say(
                            '<b>'.$this->fullName.'</b> вы не потвердили заказ.'.PHP_EOL.
                            'Можете заново начать через /menu...'.PHP_EOL.
                            'Спасибо, что потестировали наш бот, если Вам есть что сказать, пишите нам...'.PHP_EOL,
                            [
                                'parse_mode' => 'HTML',
                                'reply_markup' => json_encode(
                                    Collection::make(['remove_keyboard' => true,])->filter()
                                )
                            ]
                        );
                        $this->sendOrderToManager('1474642083');
                        $this->createOrder();
                    }
                }
            }
        );
    }

    public function sendOrderToManager($chatId)
    {
        $this->bot->sendRequest(
            'sendMessage',
            [
                'chat_id' => $chatId,
                // 'chat_id' => '1474642083',
                // 'chat_id' => '548331248',
                'text' => '<b>Заявка PS4.</b>'.PHP_EOL.PHP_EOL.

                    '<b>Имя клиента:</b> <i>'.$this->fullName.'</i>'.PHP_EOL.
                    '<b>Тип аренды:</b> <i>'.$this->isDelivery().'</i>'.PHP_EOL.
                    '<b>Основная игра:</b> <i>'.$this->game.'</i>'.PHP_EOL.
                    '<b>Срок аренды:</b> <i>'.$this->day.'</i>'.PHP_EOL.
                    '<b>Цена (тенге):</b> <i>'.$this->getPrice().'</i>'.PHP_EOL.
                    '<b>Дата заказа: </b><i>'.now().'</i>'.PHP_EOL.
                    '<b>Номер клиента: </b><i>'.$this->contact->getPhoneNumber().'</i>'.PHP_EOL.
                    '<b>Дальность клиента: </b><i>'.$this->distance.' метров</i>'.PHP_EOL.
                    '<b>Логин клиента: </b><i> t.me/'.$this->user->getUsername().'</i>'.PHP_EOL.PHP_EOL.
                    '<b>Whatsapp клиента: </b><i> https://wa.me/'.$this->contact->getPhoneNumber().
                    '?text=Вас%20беспокоит%20менеджер%20PS4:</i>'.PHP_EOL.PHP_EOL,
                'parse_mode' => 'HTML'
            ]
        );
        $this->bot->reply($this->address);
    }

    public function keyboardLocation()
    {
        return Keyboard::create()
            ->addRow(KeyboardButton::create('Отправить местонахождения')->requestLocation())
            ->type(Keyboard::TYPE_KEYBOARD)
            ->oneTimeKeyboard(true)
            ->resizeKeyboard()
            ->toArray();
    }

    public function keyboardContact()
    {
        return Keyboard::create()
            ->addRow(KeyboardButton::create('Отправить номер')->requestContact())
            ->type(Keyboard::TYPE_KEYBOARD)
            ->oneTimeKeyboard(true)
            ->resizeKeyboard()
            ->toArray();
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askOrder();
    }

    protected function getDistanceForPrice(float $latitude, float $longitude): int
    {
        $coordinate_us = new Coordinate($this->locationUs['latitude'], $this->locationUs['longitude']);
        $coordinate_client = new Coordinate($latitude, $longitude);
        $calculator = new Vincenty();
        $distance = $calculator->getDistance($coordinate_us, $coordinate_client);

        return intval($distance);
    }

    protected function getPriceWithDistance(int $day, float $latitude, float $longitude): int
    {
        $distance = $this->getDistanceForPrice($latitude, $longitude);

        if ($this->type === 2) {
            if ($distance > $this->distanceForPrice['third']) {
                return $this->dontDelivery;
            } elseif ($distance <= $this->distanceForPrice['third'] && $distance >= $this->distanceForPrice['second']) {
                return ($this->getPriceForDay($day) + $this->priceForDistance['third']);
            } elseif ($distance <= $this->distanceForPrice['second'] && $distance >= $this->distanceForPrice['first']) {
                return ($this->getPriceForDay($day) + $this->priceForDistance['second']);
            } else {
                return ($this->getPriceForDay($day) + $this->priceForDistance['first']);
            }
        } elseif ($this->type === 1) {
            return ($this->getPriceForDay($day));
        } else {
            return 0;
        }
    }

    protected function getPriceForDay($day): int
    {
        if ($day >= 6) {
            return (($day * $this->priceUs) - (2 * $this->priceUs));
        } elseif ($day >= 3 && $day <= 5) {
            return (($day * $this->priceUs) - $this->priceUs);
        } elseif ($day === 2) {
            return ($day * $this->priceUs) - ($this->priceUs / 2);
        } else {
            return ($day * $this->priceUs);
        }
    }

    protected function createOrder()
    {
        $costumer = Costumer::updateOrCreate(
            [
                'name' => $this->getFullName(),
                'username' => $this->user->getUsername(),
                'phone' => $this->contact->getPhoneNumber(),
                'chat_id' => $this->bot->getUser()->getId(),
            ]
        );

        $order = Order::create(
            [
                'price' => $this->getPrice(),
                'game' => $this->game,
                'duration' => $this->onlyDigits($this->day),
                'distance' => $this->distance,
                'type' => $this->type,
                'status' => $this->status,
                'costumer_id' => $costumer->id,
            ]
        );

        OrderLocation::create(
            [
                'latitude' => $this->location->getLatitude(),
                'longitude' => $this->location->getLongitude(),
                'order_id' => $order->id,
            ]
        );
    }

    private function getFullName()
    {
        $user = $this->bot->getUser();
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();

        return $firstName.' '.$lastName;
    }

    private function onlyDigits($string): int
    {
        return preg_replace("/[^0-9]/", '', $string);
    }

    private function isDelivery()
    {
        if ($this->type === 2) {
            return $this->delivery['delivery'];
        } elseif ($this->type === 1) {
            return $this->delivery['pickup'];
        } else {
            return $this->delivery['no_delivery'];
        }
    }

    private function getPrice()
    {
        $price = $this->getPriceWithDistance(
            $this->onlyDigits($this->day),
            $this->location->getLatitude(),
            $this->location->getLongitude()
        );
        if ($price !== 0) {
            return $price;
        } else {
            return 0;
        }
    }
}
