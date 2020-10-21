<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    public function webhook()
    {
        $telegram = Telegram::getWebhookUpdates()['message'];

        if (!TelegramUser::find($telegram['from']['id'])) {
            TelegramUser::create(json_decode($telegram['from'], true));
        }
        Telegram::commandsHandler(true);
    }
}
