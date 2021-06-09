<?php

namespace App\Conversations;

use App\Repositories\CityRepository;
use App\Repositories\UserRepository;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use Illuminate\Support\Facades\App;

class RegisterConversation extends Conversation
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

    public function collectUserInfo()
    {
        $data = [];
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
            $this->askMain();
        } else {
            $this->askLang();
        }

    }

    public function askLang()
    {
        $text = "Здравствуйте! Давайте для начала выберем язык обслуживания!\n\nKeling, avvaliga xizmat ko’rsatish tilini tanlab olaylik.\n\nHi! Let's first we choose language of serving!";

        $this->ask($text, function (Answer $answer) {
            $lang = $answer->getText();
            App::setLocale($this->lang);
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
            $this->say($this->lang);
            $messageAfterRegistration = "Les Ailes O’zbekiston muhlislarining inoq oilasiga xush kelibsiz!\nEndi esa sizga sodda va ko’p vaqt olmaydigan ro’yhatdan o’tish jarayonini taklif etamiz";
            $this->say($messageAfterRegistration);
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
        App::setLocale($this->lang);
        $messageForContact = '📱 Telefon raqamingiz qanday? Telefon raqamingizni jo\'natish uchun, quyidagi "📱 Raqamimni jo\'natish" tugmasini bosing.';
        $this->ask($messageForContact, function (Answer $answer) {
            $this->phone_number = $answer->getMessage()->getContact()->getPhoneNumber();
            $this->say(print_r($this->phone_number, true));
            $this->askCity();
        },
            Keyboard::create(Keyboard::TYPE_KEYBOARD)
                ->oneTimeKeyboard()
                ->resizeKeyboard()
                ->addRow(KeyboardButton::create('Raqamni jo\'natish?')->requestContact())
                ->toArray()
        );
    }

    public function askCity()
    {
        App::setLocale($this->lang);
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

        $this->ask("Siz qaysi shaharda istiqomat qilasiz?", function (Answer $answer) {
            $au = App::getLocale();
            $this->say(print_r($au, true));
            $this->city = $answer->getText();
            $this->user_info = $this->collectUserInfo();
            $this->say(print_r($this->user_info, true));

            $this->userRepository->addUser($this->user_info);
            $this->askMain();
        }, $keyboard->toArray());


    }

    public function askMain()
    {
        $this->ask("Juda yaxshi! Birgalikda buyurtma beramizmi? 😃", function (Answer $answer) {
//            $this->
        },
            Keyboard::create(Keyboard::TYPE_KEYBOARD)
                ->oneTimeKeyboard()
                ->resizeKeyboard()
                ->addRow(KeyboardButton::create('🛍 Buyurtma bering'))
                ->addRow(
                    KeyboardButton::create('✍ Fikrni bildirish'),
                    KeyboardButton::create('⚙ Sozlanmalar')
                )
                ->toArray()
        );
    }

    public function run()
    {
        $this->isRegistered();
    }

}
