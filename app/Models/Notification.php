<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Notification extends Model
{
    use HasFactory;

    protected $guarded=['id'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
    public function post()
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }
}
