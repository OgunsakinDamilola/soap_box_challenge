<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id', 'user_id'
    ];

    protected $appends = ['user'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id', 'id');
    }

    public function getUserAttribute()
    {
        return User::find($this->user_id);
    }
}
