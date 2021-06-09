<?php

namespace App\Conversations;

use App\Repositories\CityRepository;
use App\Repositories\UserRepository;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class RegisterConversation extends CoreConversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    protected $userRepository;
    protected $cityRepository;
    protected $telegram_id;
    protected $user_info;
    protected $phone_number;
    protected $lang;
    protected $city;

    public function __construct()
    {
        $this->userRepository = app(UserRepository::class);
        $this->cityRepository = app(CityRepository::class);
    }

    public function setLang()
    {
        app()->setLocale($this->lang);
    }

    public function collectUserInfo()
    {
        $user_info = $this->bot->getUser()->getInfo();
        $data = $user_info['user'];
        $data['telegram_id'] = $data['id'];
        $data['phone_number'] = $this->phone_number;
        $data['language_code'] = $this->lang;
        $data['city'] = $this->city;
        unset($data['id']);
        unset($data['is_bot']);

        return $data;
    }

    public function isRegistered()
    {
        $this->telegram_id = $this->bot->getUser()->getId();
        $is_registered = $this->userRepository->getUser($this->telegram_id);
        if ($is_registered) {
            $this->bot->startConversation(new MenuConversation());
        } else {
            $this->askLang();
        }

    }

    public function askLang()
    {
        $text = "Здравствуйте! Давайте для начала выберем язык обслуживания!\n\nKeling, avvaliga xizmat ko’rsatish tilini tanlab olaylik.\n\nHi! Let's first we choose language of serving!";
        $this->ask($text, function (Answer $answer) {
            $lang = $answer->getText();
            switch ($lang) {
                case "🇺🇿 O'zbekcha":
                    $this->lang = 'uz';
                    break;
                case "🇷🇺 Русский":
                    $this->lang = 'ru';
                    break;
                case "🇺🇸 English":
                    $this->lang = 'en';
                    break;
            }
            $this->setLang();
            $this->say(__('telegram.afterSelectLang'));
            $this->askContact();
        },
            Keyboard::create()
                ->type(Keyboard::TYPE_KEYBOARD)
                ->oneTimeKeyboard(true)
                ->resizeKeyboard(true)
                ->addRow(
                    KeyboardButton::create('🇺🇿 O\'zbekcha'),
                    KeyboardButton::create('🇷🇺 Русский'),
                    KeyboardButton::create('🇺🇸 English')
                )
                ->toArray()

        );
    }

    public function askContact()
    {
        $this->ask(__('telegram.askForContact'), function (Answer $answer) {
            $this->phone_number = $answer->getMessage()->getContact()->getPhoneNumber();
//            $this->say(print_r($this->phone_number, true));
            $this->setLang();
            $this->askCity();
        },
            Keyboard::create(Keyboard::TYPE_KEYBOARD)
                ->oneTimeKeyboard()
                ->resizeKeyboard()
                ->addRow(KeyboardButton::create(__('telegram.askForContactButton'))->requestContact())
                ->toArray()
        );
    }

    public function askCity()
    {
        $keyboard = Keyboard::create(Keyboard::TYPE_KEYBOARD);
        $keyboard->oneTimeKeyboard()->resizeKeyboard();
        $citiesPairs = $this->cityRepository->getCities($this->lang)->chunk(2);
        foreach ($citiesPairs as $cities) {
            $keyboards = [];
            foreach ($cities as $city) {
                $keyboards[] = KeyboardButton::create($city->title);
            }
            call_user_func_array([$keyboard, 'addRow'], $keyboards);
        }

        $this->ask(__('telegram.askCity'), function (Answer $answer) {
            $this->setLang();
//            $this->say(__('telegram.welcome'));
            $this->city = $answer->getText();
            $this->user_info = $this->collectUserInfo();
//            $this->say(print_r($this->user_info, true));

            $this->userRepository->addUser($this->user_info);
            $this->bot->startConversation(new MenuConversation());
        }, $keyboard->toArray());


    }

    public function run()
    {
        $this->isRegistered();
    }

}
