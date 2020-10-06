## Laravel-based Telegram bot

### 1. Telegram  Bot Registration and Project Creation

#### 1.1 Telegram Bot Registration

In the telegram client add contact **BotFather**, and submit `start` command Then submit command `/newbot`. Follow the hints, create your bot and add it to contacts.

#### 1.2 Laravel Setup

First, let’s move our project to a common folder. Your folder structure should look like this:-

`l`arabot/` 
  pbbtestbot`

Clone the laradock git repo inside **base_folder**:- `git clone https://github.com/Laradock/laradock.git`

Now, you **larabot** should look like this:-

`larabot/` 
  `pbbtestbot`
  `laradock(repo)`

Go to **laradock/nginx/sites** and make copy of **laravel.conf.example** rename one to **pbbtestbot**. Now, open pbbtestbot.conf in a text editor and make the following changes:-

`server_name pbbtestbot.test`
`root /var/www/pbbtestbot/public`

Open your hosts file(/etc/hosts) in a text editor and add following add the end:-

`127.0.0.1 pbbtestbot.test`

Now, the configurations are done. Now, Let’s build and start our projects.

First of all, download and install Docker for your system. You can google it or download it from [here](https://docs.docker.com/docker-for-mac/).

> **NOTE**:- Make sure docker is running in the background after installing it.

Move to laradock(repo) folder and copy **env-example** to **.env**

`cp env-example .env`

Now we will build our containers.

`docker-compose up -d nginx mysql phpmyadmin`

It might take some to build again(depending on your internet speed)

Now, To run **Composer, Artisan** commands start workspace

`docker-compose exec workspace bash`

**One last thing**, In your project’s `.env` file change `DB_HOST` to `mysql`

First, download the Laravel installer using Composer:

`composer global require laravel/installer`

Once installed, the `laravel new` command will create a fresh Laravel installation in the directory you specify.

#### 1.3 Authentication Setup

Pull first-party package through composer from this directory and install it as a dev dependency

`composer require laravel/ui --dev`

Now we can create user interface with authentication and front-end we choose (bootstrap, vue, react)

`php artisan ui vue --auth`

Run `npm install && npm run dev` to compile your fresh scaffolding. This is our dependencies.

#### 1.4 Deployment Pre-requests

##### 1.4.1 Create initial data in database (default user). 

For this goal we create Seeder class

`php artisan make:seeder UsersTable`

Now we need to revise our UserFactory for administrator and define run() method in seeder

DB

In your .env file put double quotes around your database password and username then run `php artisan config:clear` and `php artisan cache:clear`

##### 1.4.2 Prevent unauthorized registration in routes.

First approach

```php
//  routes that match methods post/get and registration path
//  execute function at attempt to access certain route
//  use Facade auth and logout method
//  and return to main page
Route::match(['post', 'get'], 'register', function () {
    \Illuminate\Support\Facades\Auth::logout();
    return redirect('/');
})->name('register');
```

Second approach is to define middleware in RegisterController

```php
/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
```

### 2. WebHook Pre-requests

Registration mechanism allows us to define where telegram server will send requests from messenger. It is simple and consist of sending collection of data to special address. 

We need functionality to have an option of dns changing. We need to create administrative part for bot managing - state page, and page with system configuration.

#### 2.1 Dashboard

1. `php artisan make:controller Backend/DashboardController` . Define method index to return ***backend.index.blade.php*** view

2. Create directory and view

3. Define grouped routing - if we don't want to define in every controller mechanism for http-request filtration (like only authenticated user can request to them) - we define it in route. It will obtain prefix **admin**, namepsace **backend** (all controllers stored in this directory and named routes **admin.** 

   ```php
   Route::middleware(['auth'])->prefix('admin')->namespace('Backend')->name('admin.')->group(function () {
       Route::get('/', [DashboardController::class, 'index'])->name('index');
   }
   );
   ```

4. Add reference for named route of status bar 

   ```html
   <!-- Left Side Of Navbar -->
   <ul class="navbar-nav mr-auto">
       <li><a href="{{ route('admin.index') }}"></a>Status Bar</li>
   </ul>
   ```

5. Create migration for storing settings in database. We use approach that give us opportunity to create new data with minimal steps. `php artisan make:model Setting -m` and define schema 

   ```php
   Schema::create('settings', function (Blueprint $table) {
       $table->string('key', 40)->index()->unique();
       $table->mediumText('value');
       //  if in value field will be stored serialized type of data (JSON)
       $table->boolean()->default(0);
   });
   ```

6. Create model for storing settings in database. Define business logic. 

   ```php
   /**
    * Extract settings from db as key-value pair
    * Added parameter for extracting certain setting
    * @param null $key
    * @return \Illuminate\Support\Collection
    */
   public static function getSettings($key = null)
   {
       $settings = $key ? self::where('key', $key)->first() : self::get();
       $collect = collect();
       foreach ($settings as $key => $value) {
           $collect->put($settings->key, $settings->value);
       }
       return $collect;
   }
   ```

7. Create controller for storing settings in database 

   ```php
   /**
    * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
    */
   public function index()
   {
       return view('backend.setting', Setting::getSettings());
   }
   ```

   And create view.

8. In controller create method for fire-up storage into database 

   ```php
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
   ```

9. Create route for list of settings

   ```
   Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
   Route::post('/setting/store', [SettingController::class, 'store'])->name('setting.store');
   ```

10. Define setting,blade.php with form for URL callback and dropdown list . Note that Webhook will work only with https protocol.

    ```php+HTML
    <div class="form-group">
        <label>URL Callback for Telegram Bot</label>
        <div class="input-group">
            <div class="input-group-btn">
                <ul class="nav nav-pills">
                    <button type="button"
                            class="btn btn-default dropdown-toggle"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                    >
                        Action
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="#"
                               onclick="document.getElementById('url_callback_bot') . value = '{{ url('') }}'">Insert
                                URL</a></li>
                        <li><a href="#">Send URL</a></li>
                        <li><a href="#">Get Info</a></li>
                        <li><a href="#"></a></li>
                    </ul>
                </ul>
            </div>
            {{--                                    Field with settings--}}
            <input type="url"
                   class="form-control"
                   id="url_callback_bot"
                   name="url_callback_bot"
                   value="{{ $url_callback_bot ?? '' or '' }}">
        </div>
    </div>
    <button class="btn btn-primary" type="submit">Save</button>
    ```

11. To add new setting we need to add new filed template 

### 3. Webhook Registration

#### 3.1 Telegram Bot API - PHP SDK

80% of actual work with telegram bot is already created for us in Telegram Bot API - PHP SDK (https://github.com/irazasyed/telegram-bot-sdk) .

1. Install via composer.

2. Add the Service Provider 

   ```php
   Telegram\Bot\Laravel\TelegramServiceProvider::class
   ```

3. Add the alias (optional)

   ```php
    'Telegram' => Telegram\Bot\Laravel\Facades\Telegram::class,
   ```

4. Before publishing Telegram config, clear the cache, because we alter list of service providers (this config cached) 

   ```bash
   php artisan config:cache
   ```

5. Run command to publish config for Telegram

   ```
   php artisan vendor:publish --provider="Telegram\Bot\Laravel\TelegramServiceProvider"
   ```

6. In the telegram.php `'token'` will hold value of our received bot_token, 'commands' will hold commands. Put token in the array

7. Clear cache

#### 3.2 Guzzle

https://github.com/guzzle/guzzle Guzzle is a PHP HTTP client that makes it easy to send HTTP requests and trivial to integrate with web services. Abstraction layer for http request it uses **cURL** by default. **cURL** – tool for allowing you to access data across the web and different web-sites. PHP supports **libcurl**, a library created by **Daniel Stenberg**, that allows you to connect and communicate to many different types of servers with many different types of protocols. libcurl currently supports the http, https, ftp, gopher, telnet, dict, file, and ldap protocols. **libcurl** also supports HTTPS certificates, HTTP POST, HTTP PUT, FTP uploading (this can also be done with PHP's ftp extension), HTTP form based upload, proxies, cookies, and user+password authentication. cURL allows you to send request – you can specify URL. With curl we can fetch the whole web-page, but more often we can send a request to some sort of form that requires _POST data or search that requires _GET data and return it in actual array format, xml. JSON.

#### 3.3 Methods for Handling Actions

Actions **send URL** and **get information** will work with telegram API, so it better to use POST, because it checks authentication.

First form will set URL address from our input as an address for sending all requests from chat of our bot.

Second form will retrieve information with already existing settings.

Let's create method that will be called for sending data to telegram API, and it will accept parameters for reuse of code in SettingController. Base URI of client merged with relative URI. 

```php
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
    $result = $this->sendTelegramData('setwebhook', [
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

    $client = new Client(['base_uri' => 'https://api.telegram.org/bot' . Telegram::getAcessToken() . '/']);

    $result = $client->request($method, $route, $params);

    return (string) $result->getBody();
}
```

Create Routes 

```php
Route::post('setting/setwebhook', [SettingController::class, 'setwebhook'])->name('setting.setwebhook');
Route::post('setting/getwebhookinfo', [SettingController::class, 'getwebhookinfo'])->name('setting.getwebhookinfo');
```

Revise our template

```php
@if(Session::has('status'))
    <div class="alert-info">
        <span>{{ Session::get('status') }}</span>
    </div>
@endif
```



## Credentials

### Laravel

License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

### Telegram Bot API - PHP SDK

License

This project is released under the [BSD 3-Clause](https://github.com/irazasyed/telegram-bot-sdk/blob/master/LICENSE) License.

