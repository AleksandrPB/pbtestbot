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

        $this->replyWithMessage(['text' => 'User\'s email' . $user->email]);

        $telegram_user = Telegram::getWebhookUpdates()['message'];

        $text = sprintf('%s: %s'.PHP_EOL, 'Chat id', $telegram_user['from']['id']);
        $text .= sprintf('%s: %s'.PHP_EOL, 'Username', $telegram_user['from']['username']);

        $this->replyWithMessage(compact('text'));
    }
}
