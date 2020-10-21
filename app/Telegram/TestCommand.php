<?php


namespace App\Telegram;

use App\Models\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;


/**
 * Class TestCommand.
 */
class TestCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'test';

    /**
     * @var array Command Aliases
     */
//    protected $aliases = ['listcommands'];

    /**
     * @var string Command Description
     */
    protected $description = 'Test command, Get a list of commands';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $user = User::find(1);

        $this->replyWithMessage(['text' => 'User email ' . $user->email]);

        $telegram_user = Telegram::getWebhookUpdates()['message'];

        $text = sprintf('%s: %s' . PHP_EOL, 'Chat id', $telegram_user['from']['id']);
        $text .= sprintf('%s: %s' . PHP_EOL, 'Username', $telegram_user['from']['username']);

//        $keyboard = [
//            ['7', '8', '9'],
//            ['4', '5', '6'],
//            ['1', '2', '3'],
//            ['0']
//        ];
//
//        $reply_markup = Telegram::replyKeyboardMarkup(
//            [
//                'keyboard' => $keyboard,
//                'resize_keyboard' => true,
//                'one_time_keyboard' => true
//            ]
//        );
//
//        $response = Telegram::sendMessage(
//            [
//                'chat_id' => $telegram_user['from']['id'],
//                'text' => 'Hello World',
//                'reply_markup' => $reply_markup
//            ]
//        );
//
//        $messageId = $response->getMessageId();

        $this->replyWithMessage(compact('text'));
    }
}
