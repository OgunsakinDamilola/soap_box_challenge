<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id', 'name', 'slug', 'description'
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(ChannelUser::class, 'channel_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'channel_id', 'id');
    }
}
