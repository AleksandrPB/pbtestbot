<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

class Setting extends Model
{
    use HasFactory;

    //  we need to define this because we do not have
    //  create_at|update_at at schema
    public $timestamps = false;


    public static function getSettings($key = null)
    {
        $settings = $key ? Setting::where('key', $key)->first() : Setting::get();
        $collect = collect();
        foreach ($settings as $setting) {
            $collect->put($setting->key, $setting->value);
        }
        return $collect;
    }
}
