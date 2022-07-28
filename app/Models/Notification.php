<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $guarded=['id'];

    public function sender() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function recipient() : BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
    public function quote() : BelongsTo
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }
}
