<?php

namespace App\Conversations;

use App\Repositories\UserRepository;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class MenuConversation extends CoreConversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */

    protected $userRepository;
    protected $telegram_id;

    public function __construct()
    {
        $this->userRepository = app(UserRepository::class);
    }

    public function setLang()
    {
        $this->telegram_id = $this->bot->getUser()->getId();
        $user = $this->userRepository->getUser($this->telegram_id);
        app()->setLocale($user->language_code);
        $this->say(app()->getLocale());
    }


    public function askMain()
    {
        $this->ask("Juda yaxshi! Birgalikda buyurtma beramizmi? 😃", function (Answer $answer) {
            $this->setLang();
            $road = $answer->getText();
            parent::redirectMain($road);
        },
            Keyboard::create(Keyboard::TYPE_KEYBOARD)
                ->oneTimeKeyboard()
                ->resizeKeyboard()
                ->addRow(KeyboardButton::create(__('telegram.menu.order')))
                ->addRow(
                    KeyboardButton::create(__('telegram.menu.feedback')),
                    KeyboardButton::create(__('telegram.menu.settings'))
                )
                ->toArray()
        );
    }

    public function askSettings()
    {
        $this->ask(__('telegram.menu.settings'), function (Answer $answer) {
            $this->setLang();
            $this->say(app()->getLocale());
            $settingsRoad = $answer->getText();
            parent::redirectSettings($settingsRoad);
        }, Keyboard::create(Keyboard::TYPE_KEYBOARD)
            ->oneTimeKeyboard()
            ->resizeKeyboard()
            ->addRow(
                KeyboardButton::create(__('telegram.settings.changeName')),
                KeyboardButton::create(__('telegram.settings.changeNumber'))
            )
            ->addRow(
                KeyboardButton::create(__('telegram.settings.changeLang')),
                KeyboardButton::create(__('telegram.settings.changeCity'))
            )
            ->addRow(
                KeyboardButton::create(__('telegram.back'))
            )
            ->toArray()

        );
    }

    public function run()
    {
        $this->askMain();
    }
}
