<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Setting;
use Telegram\Bot\Laravel\Facades\Telegram;

class SettingController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('backend.setting', Setting::getSettings());
    }

    public function store(Request $request)
    {
        //  1. Remove all values from settings table because we do not obtain method fo refresh data
        Setting::where('key', '!=', NULL)->delete();
        //  2. Go through all values of our form fields. Exclude token. Create instance of model.
        //  Assign field key.
        foreach ($request->except('_token') as $key => $value) {
            $setting = new Setting;
            $setting->key = $key;
            $setting->value = $request->$key;
            $setting->save();
        }
        return redirect()->route('admin.setting.index');

    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function getwebhookinfo (Request $request)
    {
        $result = $this->sendTelegramData('getWebhookinfo');
        return redirect()->route('admin.setting.index')->with('status', $result);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function setwebhook (Request $request)
    {
        $result = $this->sendTelegramData('setWebhook', [
            'query' => ['url' => $request->url . '/' . Telegram::getAccessToken()]
        ]);
        return redirect()->route('admin.setting.index')->with('status', $result);
    }

    /**
     * Create guzzle client and pass one argument with base URI for bot
     * relative URI
     * @param string $route
     * parameter from request string relative to URI scheme, key-value pair
     * @param array $params
     * http request method
     * @param string $method
     * string is obligatory
     * @return string
     * @throws GuzzleException
     */
    public function sendTelegramData($route = '', $params = [], $method = 'POST')
    {

        $client = new Client(['base_uri' => 'https://api.telegram.org/bot' . Telegram::getAccessToken() . '/']);

        $result = $client->request($method, $route, $params);

        return (string) $result->getBody();
    }


}
