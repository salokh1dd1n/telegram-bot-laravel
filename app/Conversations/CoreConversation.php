<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

abstract class CoreConversation extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */


    public function redirectMain($text)
    {
        switch ($text) {
            case __('telegram.menu.order'):
                 return $this->askOrder();
                break;
            case __('telegram.menu.feedback'):
                $this->say("Feedback");
                 return $this->askFeedback();
                break;
            case __('telegram.menu.settings'):
                return $this->askSettings();
                break;
        }
    }

    public function redirectSettings($text)
    {
        switch ($text) {
            case __('telegram.settings.changeName'):
                 return $this->editName();
                break;
            case __('telegram.settings.changeNumber'):
                 return $this->editNumber();
                break;
            case __('telegram.settings.changeLang'):
                return $this->editLang();
                break;
            case __('telegram.settings.changeCity'):
                return $this->editCity();
                break;
        }
    }


    public function run()
    {
        //
    }
}
