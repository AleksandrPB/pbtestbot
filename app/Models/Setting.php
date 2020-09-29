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
}
