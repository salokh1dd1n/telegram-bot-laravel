<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class TestConversation extends Conversation
{
    protected $firstname;

    protected $email;

    public function askFirstname()
    {
        $this->ask('Hello! What is your firstname?', function(Answer $answer) {
            // Save result
            $this->firstname = $answer->getText();
            $this->say('Nice to meet you '.$this->firstname);
            $this->askEmail();
        });
    }

    public function askEmail()
    {
        $this->ask('One more thing - what is your email?', function(Answer $answer) {
            // Save result
            $this->email = $answer->getText();

            $this->say('Great - that is all we need, '.$this->firstname);
            $this->say(print_r($answer->getText()));
        });
    }

    public function askContact()
    {
        $this->askForContact('Your contact?', function (Answer $answer) {
            $this->say($answer->getText());
            },null,
            Keyboard::create(Keyboard::TYPE_KEYBOARD)
                ->oneTimeKeyboard()
                ->resizeKeyboard()
                ->addRow(KeyboardButton::create('Send contact')->requestContact())
                ->toArray()

        );
    }

    public function askLang()
    {
        $this->ask('Your contact?', function (Answer $answer) {
            $this->say($answer->getText());
        },null,
            Keyboard::create(Keyboard::TYPE_KEYBOARD)
                ->oneTimeKeyboard()
                ->resizeKeyboard()
                ->addRow(
                    KeyboardButton::create('Send contact')->requestContact()
                )
                ->toArray()
        );
    }

    public function run()
    {
        // This will be called immediately
        $this->askFirstname();
    }
}
