<?php

namespace App\Conversations;

use App\Models\Person;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class SearchConversation extends Conversation
{
    public function askFullName(): void
    {
        $this->ask(self::getQuestion(), function(Answer $answer) {
            $this->say('Пошук...');

            list($fullName, $birthDate) = self::parseText($answer->getText());
            $person = Person::findByFullName($fullName, $birthDate);

            if ($person) {
                Request::initialize(new Telegram(config('botman.telegram.token'), 'mvswantedbot'));

                Request::sendPhoto([
                    'chat_id' => $this->bot->getUser()->getId(),
                    'caption' => $person->__toString(),
                    'photo' => Request::encodeFile($person->photo_url),
                ]);
            } else {
                $this->say('За цими даними нікого не знайдено');
            }
        });
    }

    public function run(): void
    {
        $this->askFullName();
    }

    private static function getQuestion(): string
    {
        $question = 'Введіть ПІБ, строго в цьому порядку. Для того, щоб звузити пошук, додайте дату народження.' . PHP_EOL;
        $question .= 'Приклад: Іванов Іван Іванович, 26.05.1992';

        return $question;
    }

    private static function parseText(string $text): array
    {
        $result = explode(',', $text);

        return [$result[0], $result[1] ?? null];
    }
}
