<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description'
    ];

    public function channels()
    {
        return $this->hasMany(Channel::class, 'workspace_id', 'id');
    }

    public function owner()
    {
        return $this->hasOne(WorkspaceUser::class, 'workspace_id', 'id')->where('owner', true);
    }

    public function users()
    {
        return $this->hasMany(WorkspaceUser::class, 'workspace_id', 'id')->orderBy('id', 'desc');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'workspace_id', 'id');
    }
}
