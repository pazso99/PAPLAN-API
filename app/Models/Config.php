<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'config';
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key) {
        return json_decode(
            self::where('key', $key)->value('value')
        );
    }

    public static function setValue(string $key, $value) {
        self::where('key', $key)->update(['value' => json_encode($value)]);
    }
}
